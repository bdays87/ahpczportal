<?php

namespace App\implementations;

use App\Interfaces\iaccountingperiodInterface;
use App\Interfaces\iarreceiptInterface;
use App\Interfaces\iaudittrailInterface;
use App\Interfaces\ijournalentryInterface;
use App\Models\AccountsReceivableInvoice;
use App\Models\AccountsReceivableReceipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class _arreceiptRepository implements iarreceiptInterface
{
    protected $model;
    protected $invoice;
    protected $journalRepo;
    protected $periodRepo;
    protected $auditRepo;

    public function __construct(
        AccountsReceivableReceipt $model,
        AccountsReceivableInvoice $invoice,
        ijournalentryInterface $journalRepo,
        iaccountingperiodInterface $periodRepo,
        iaudittrailInterface $auditRepo
    ) {
        $this->model       = $model;
        $this->invoice     = $invoice;
        $this->journalRepo = $journalRepo;
        $this->periodRepo  = $periodRepo;
        $this->auditRepo   = $auditRepo;
    }

    public function getAll($search = null, $status = null, $customerId = null)
    {
        return $this->model
            ->with('customer', 'currency')
            ->when($search,     fn($q) => $q->where('receipt_number', 'like', "%$search%")->orWhere('reference', 'like', "%$search%"))
            ->when($status,     fn($q) => $q->where('status', $status))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->orderByDesc('receipt_date')
            ->paginate(50);
    }

    public function get($id)
    {
        return $this->model
            ->with('customer', 'currency', 'arAccount', 'cashGlAccount', 'bankAccount.bank', 'invoices', 'journalEntry', 'createdBy')
            ->find($id);
    }

    public function create($data, $allocations)
    {
        DB::beginTransaction();
        try {
            $totalAllocated = collect($allocations)->sum('amount_allocated');
            if ($totalAllocated > $data['amount']) {
                return ['status' => 'error', 'message' => 'Allocated amount exceeds receipt amount'];
            }

            $data['uuid']           = Str::uuid()->toString();
            $data['receipt_number'] = $this->generateReceiptNumber();
            $data['createdby']      = Auth::id();
            $data['status']         = 'DRAFT';
            $receipt = $this->model->create($data);

            foreach ($allocations as $alloc) {
                $inv = $this->invoice->find($alloc['ar_invoice_id']);
                if (!$inv) continue;
                if ($alloc['amount_allocated'] > $inv->balance_due) {
                    DB::rollBack();
                    return ['status' => 'error', 'message' => "Allocation for {$inv->invoice_number} exceeds its balance"];
                }
                $receipt->invoices()->attach($inv->id, ['amount_allocated' => $alloc['amount_allocated']]);
                $newReceived   = $inv->amount_received + $alloc['amount_allocated'];
                $newBalanceDue = $inv->total_amount - $newReceived;
                $newStatus     = $newBalanceDue <= 0 ? 'PAID' : 'PARTIALLY_PAID';
                $inv->update(['amount_received' => $newReceived, 'balance_due' => $newBalanceDue, 'status' => $newStatus]);
            }

            $this->auditRepo->log('AR', 'CREATE_RECEIPT', AccountsReceivableReceipt::class, $receipt->id, $receipt->receipt_number, null, $data);
            DB::commit();
            return ['status' => 'success', 'message' => 'Receipt created successfully', 'data' => $receipt->id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $receipt = $this->model->with('invoices')->find($id);
            if (!$receipt) return ['status' => 'error', 'message' => 'Receipt not found'];
            if ($receipt->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT receipts can be deleted'];

            foreach ($receipt->invoices as $inv) {
                $allocated      = $inv->pivot->amount_allocated;
                $newReceived    = $inv->amount_received - $allocated;
                $newBalanceDue  = $inv->total_amount - $newReceived;
                $newStatus      = $newBalanceDue >= $inv->total_amount ? 'POSTED' : 'PARTIALLY_PAID';
                $inv->update(['amount_received' => $newReceived, 'balance_due' => $newBalanceDue, 'status' => $newStatus]);
            }

            $receipt->invoices()->detach();
            $receipt->delete();

            $this->auditRepo->log('AR', 'DELETE_RECEIPT', AccountsReceivableReceipt::class, $id, $receipt->receipt_number, null, null);
            DB::commit();
            return ['status' => 'success', 'message' => 'Receipt deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function post($id)
    {
        DB::beginTransaction();
        try {
            $receipt = $this->model->find($id);
            if (!$receipt) return ['status' => 'error', 'message' => 'Receipt not found'];
            if ($receipt->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT receipts can be posted'];

            $period = $this->periodRepo->getCurrentPeriod();
            if (!$period) { DB::rollBack(); return ['status' => 'error', 'message' => 'No open accounting period']; }

            // DR Cash/Bank / CR Accounts Receivable
            $lines = [
                [
                    'account_id'  => $receipt->cash_gl_account_id,
                    'description' => 'AR Receipt: ' . $receipt->receipt_number,
                    'debit'       => $receipt->amount,
                    'credit'      => 0,
                ],
                [
                    'account_id'  => $receipt->ar_account_id,
                    'description' => 'AR Receipt: ' . $receipt->receipt_number,
                    'debit'       => 0,
                    'credit'      => $receipt->amount,
                ],
            ];

            $jeData = [
                'entry_date'           => $receipt->receipt_date->toDateString(),
                'description'          => 'AR Receipt: ' . $receipt->receipt_number,
                'entry_type'           => 'AR_RECEIPT',
                'source'               => 'accounts_receivable_receipts',
                'source_id'            => $receipt->id,
                'accounting_period_id' => $period->id,
                'currency_id'          => $receipt->currency_id,
                'exchange_rate'        => $receipt->exchange_rate,
            ];

            $result = $this->journalRepo->create($jeData, $lines);
            if ($result['status'] !== 'success') { DB::rollBack(); return $result; }

            $jeId = $result['data'];
            $this->journalRepo->post($jeId);

            $receipt->update(['status' => 'POSTED', 'journal_entry_id' => $jeId]);

            $this->auditRepo->log('AR', 'POST_RECEIPT', AccountsReceivableReceipt::class, $id, $receipt->receipt_number, null, ['status' => 'POSTED']);
            DB::commit();
            return ['status' => 'success', 'message' => 'Receipt posted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function cancel($id)
    {
        try {
            $receipt = $this->model->find($id);
            if (!$receipt) return ['status' => 'error', 'message' => 'Receipt not found'];
            if ($receipt->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT receipts can be cancelled'];
            $receipt->update(['status' => 'CANCELLED']);
            $this->auditRepo->log('AR', 'CANCEL_RECEIPT', AccountsReceivableReceipt::class, $id, $receipt->receipt_number, null, ['status' => 'CANCELLED']);
            return ['status' => 'success', 'message' => 'Receipt cancelled successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function generateReceiptNumber(): string
    {
        $year  = date('Y');
        $count = $this->model->whereYear('created_at', $year)->count() + 1;
        return 'ARR-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
