<?php

namespace App\implementations;

use App\Interfaces\iaccountingperiodInterface;
use App\Interfaces\iappaymentInterface;
use App\Interfaces\iaudittrailInterface;
use App\Interfaces\ijournalentryInterface;
use App\Models\AccountsPayableInvoice;
use App\Models\AccountsPayablePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class _appaymentRepository implements iappaymentInterface
{
    protected $model;
    protected $invoice;
    protected $journalRepo;
    protected $periodRepo;
    protected $auditRepo;

    public function __construct(
        AccountsPayablePayment $model,
        AccountsPayableInvoice $invoice,
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

    public function getAll($search = null, $status = null, $supplierId = null)
    {
        return $this->model
            ->with('supplier', 'currency')
            ->when($search,     fn($q) => $q->where('payment_number', 'like', "%$search%")->orWhere('reference', 'like', "%$search%"))
            ->when($status,     fn($q) => $q->where('status', $status))
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->orderByDesc('payment_date')
            ->paginate(50);
    }

    public function get($id)
    {
        return $this->model
            ->with('supplier', 'currency', 'apAccount', 'bankGlAccount', 'bankAccount.bank', 'invoices', 'journalEntry', 'createdBy')
            ->find($id);
    }

    public function create($data, $allocations)
    {
        DB::beginTransaction();
        try {
            $totalAllocated = collect($allocations)->sum('amount_allocated');
            if ($totalAllocated > $data['amount']) {
                return ['status' => 'error', 'message' => 'Allocated amount exceeds payment amount'];
            }

            $data['uuid']           = Str::uuid()->toString();
            $data['payment_number'] = $this->generatePaymentNumber();
            $data['createdby']      = Auth::id();
            $data['status']         = 'DRAFT';
            $payment = $this->model->create($data);

            // Save allocations and update invoice balances
            foreach ($allocations as $alloc) {
                $inv = $this->invoice->find($alloc['ap_invoice_id']);
                if (!$inv) continue;
                if ($alloc['amount_allocated'] > $inv->balance_due) {
                    DB::rollBack();
                    return ['status' => 'error', 'message' => "Allocation for invoice {$inv->invoice_number} exceeds its balance"];
                }
                $payment->invoices()->attach($inv->id, ['amount_allocated' => $alloc['amount_allocated']]);

                $newAmountPaid  = $inv->amount_paid + $alloc['amount_allocated'];
                $newBalanceDue  = $inv->total_amount - $newAmountPaid;
                $newStatus      = $newBalanceDue <= 0 ? 'PAID' : 'PARTIALLY_PAID';
                $inv->update(['amount_paid' => $newAmountPaid, 'balance_due' => $newBalanceDue, 'status' => $newStatus]);
            }

            $this->auditRepo->log('AP', 'CREATE_PAYMENT', AccountsPayablePayment::class, $payment->id, $payment->payment_number, null, $data);
            DB::commit();
            return ['status' => 'success', 'message' => 'Payment created successfully', 'data' => $payment->id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $payment = $this->model->with('invoices')->find($id);
            if (!$payment) return ['status' => 'error', 'message' => 'Payment not found'];
            if ($payment->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT payments can be deleted'];

            // Reverse invoice balances
            foreach ($payment->invoices as $inv) {
                $allocated     = $inv->pivot->amount_allocated;
                $newAmountPaid = $inv->amount_paid - $allocated;
                $newBalanceDue = $inv->total_amount - $newAmountPaid;
                $newStatus     = $newBalanceDue >= $inv->total_amount ? 'POSTED' : 'PARTIALLY_PAID';
                $inv->update(['amount_paid' => $newAmountPaid, 'balance_due' => $newBalanceDue, 'status' => $newStatus]);
            }

            $payment->invoices()->detach();
            $payment->delete();

            $this->auditRepo->log('AP', 'DELETE_PAYMENT', AccountsPayablePayment::class, $id, $payment->payment_number, null, null);
            DB::commit();
            return ['status' => 'success', 'message' => 'Payment deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function post($id)
    {
        DB::beginTransaction();
        try {
            $payment = $this->model->with('supplier')->find($id);
            if (!$payment) return ['status' => 'error', 'message' => 'Payment not found'];
            if ($payment->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT payments can be posted'];

            $period = $this->periodRepo->getCurrentPeriod();
            if (!$period) { DB::rollBack(); return ['status' => 'error', 'message' => 'No open accounting period']; }

            // DR Accounts Payable / CR Bank
            $lines = [
                [
                    'account_id'  => $payment->ap_account_id ?? $payment->supplier->ap_account_id,
                    'description' => 'AP Payment: ' . $payment->payment_number,
                    'debit'       => $payment->amount,
                    'credit'      => 0,
                ],
                [
                    'account_id'  => $payment->bank_gl_account_id,
                    'description' => 'AP Payment: ' . $payment->payment_number,
                    'debit'       => 0,
                    'credit'      => $payment->amount,
                ],
            ];

            $jeData = [
                'entry_date'           => $payment->payment_date->toDateString(),
                'description'          => 'AP Payment: ' . $payment->payment_number,
                'entry_type'           => 'AP_PAYMENT',
                'source'               => 'accounts_payable_payments',
                'source_id'            => $payment->id,
                'accounting_period_id' => $period->id,
                'currency_id'          => $payment->currency_id,
                'exchange_rate'        => $payment->exchange_rate,
            ];

            $result = $this->journalRepo->create($jeData, $lines);
            if ($result['status'] !== 'success') { DB::rollBack(); return $result; }

            $jeId = $result['data'];
            $this->journalRepo->post($jeId);

            $payment->update(['status' => 'POSTED', 'journal_entry_id' => $jeId]);

            $this->auditRepo->log('AP', 'POST_PAYMENT', AccountsPayablePayment::class, $id, $payment->payment_number, null, ['status' => 'POSTED']);
            DB::commit();
            return ['status' => 'success', 'message' => 'Payment posted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function cancel($id)
    {
        try {
            $payment = $this->model->find($id);
            if (!$payment) return ['status' => 'error', 'message' => 'Payment not found'];
            if ($payment->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT payments can be cancelled'];
            $payment->update(['status' => 'CANCELLED']);
            $this->auditRepo->log('AP', 'CANCEL_PAYMENT', AccountsPayablePayment::class, $id, $payment->payment_number, null, ['status' => 'CANCELLED']);
            return ['status' => 'success', 'message' => 'Payment cancelled successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function generatePaymentNumber(): string
    {
        $year  = date('Y');
        $count = $this->model->whereYear('created_at', $year)->count() + 1;
        return 'APY-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
