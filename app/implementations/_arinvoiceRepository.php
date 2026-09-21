<?php

namespace App\implementations;

use App\Interfaces\iaccountingperiodInterface;
use App\Interfaces\iarinvoiceInterface;
use App\Interfaces\iaudittrailInterface;
use App\Interfaces\ijournalentryInterface;
use App\Models\AccountsReceivableInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class _arinvoiceRepository implements iarinvoiceInterface
{
    protected $model;
    protected $journalRepo;
    protected $periodRepo;
    protected $auditRepo;

    public function __construct(
        AccountsReceivableInvoice $model,
        ijournalentryInterface $journalRepo,
        iaccountingperiodInterface $periodRepo,
        iaudittrailInterface $auditRepo
    ) {
        $this->model       = $model;
        $this->journalRepo = $journalRepo;
        $this->periodRepo  = $periodRepo;
        $this->auditRepo   = $auditRepo;
    }

    public function getAll($search = null, $status = null, $customerId = null)
    {
        return $this->model
            ->with('customer', 'currency')
            ->when($search,     fn($q) => $q->where('invoice_number', 'like', "%$search%")->orWhere('customer_name', 'like', "%$search%"))
            ->when($status,     fn($q) => $q->where('status', $status))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->orderByDesc('invoice_date')
            ->paginate(50);
    }

    public function get($id)
    {
        return $this->model
            ->with('customer', 'currency', 'incomeAccount', 'arAccount', 'costCenter', 'taxRate', 'journalEntry', 'receipts', 'createdBy')
            ->find($id);
    }

    public function create($data)
    {
        try {
            $data['uuid']           = Str::uuid()->toString();
            $data['invoice_number'] = $this->generateInvoiceNumber();
            $data['createdby']      = Auth::id();
            $data['status']         = 'DRAFT';
            $data['balance_due']    = $data['total_amount'];
            $data['amount_received'] = 0;
            $invoice = $this->model->create($data);
            $this->auditRepo->log('AR', 'CREATE', AccountsReceivableInvoice::class, $invoice->id, $invoice->invoice_number, null, $data);
            return ['status' => 'success', 'message' => 'AR invoice created successfully', 'data' => $invoice->id];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $invoice = $this->model->find($id);
            if (!$invoice) return ['status' => 'error', 'message' => 'Invoice not found'];
            if ($invoice->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT invoices can be edited'];
            $data['balance_due'] = $data['total_amount'];
            $old = $invoice->toArray();
            $invoice->update($data);
            $this->auditRepo->log('AR', 'UPDATE', AccountsReceivableInvoice::class, $id, $invoice->invoice_number, $old, $data);
            return ['status' => 'success', 'message' => 'AR invoice updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $invoice = $this->model->find($id);
            if (!$invoice) return ['status' => 'error', 'message' => 'Invoice not found'];
            if (!in_array($invoice->status, ['DRAFT', 'CANCELLED'])) {
                return ['status' => 'error', 'message' => 'Only DRAFT or CANCELLED invoices can be deleted'];
            }
            $invoice->delete();
            $this->auditRepo->log('AR', 'DELETE', AccountsReceivableInvoice::class, $id, $invoice->invoice_number, null, null);
            return ['status' => 'success', 'message' => 'AR invoice deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function post($id)
    {
        DB::beginTransaction();
        try {
            $invoice = $this->model->find($id);
            if (!$invoice) return ['status' => 'error', 'message' => 'Invoice not found'];
            if ($invoice->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT invoices can be posted'];

            $period = $this->periodRepo->getCurrentPeriod();
            if (!$period) { DB::rollBack(); return ['status' => 'error', 'message' => 'No open accounting period']; }

            // DR Accounts Receivable / CR Income
            $lines = [
                [
                    'account_id'     => $invoice->ar_account_id,
                    'description'    => 'AR Invoice: ' . $invoice->invoice_number,
                    'debit'          => $invoice->total_amount,
                    'credit'         => 0,
                    'cost_center_id' => $invoice->cost_center_id,
                ],
                [
                    'account_id'     => $invoice->income_account_id,
                    'description'    => 'Income: ' . $invoice->invoice_number,
                    'debit'          => 0,
                    'credit'         => $invoice->subtotal,
                    'cost_center_id' => $invoice->cost_center_id,
                ],
            ];
            if ($invoice->tax_amount > 0) {
                $taxAccountId = optional($invoice->taxRate)->salesAccount?->id ?? $invoice->income_account_id;
                $lines[] = [
                    'account_id'  => $taxAccountId,
                    'description' => 'Output Tax: ' . $invoice->invoice_number,
                    'debit'       => 0,
                    'credit'      => $invoice->tax_amount,
                ];
            }

            $jeData = [
                'entry_date'           => $invoice->invoice_date->toDateString(),
                'description'          => 'AR Invoice: ' . $invoice->invoice_number,
                'entry_type'           => 'AR_INVOICE',
                'source'               => 'accounts_receivable_invoices',
                'source_id'            => $invoice->id,
                'accounting_period_id' => $period->id,
                'currency_id'          => $invoice->currency_id,
                'exchange_rate'        => $invoice->exchange_rate,
            ];

            $result = $this->journalRepo->create($jeData, $lines);
            if ($result['status'] !== 'success') { DB::rollBack(); return $result; }

            $jeId = $result['data'];
            $this->journalRepo->post($jeId);

            $invoice->update([
                'status'               => 'POSTED',
                'journal_entry_id'     => $jeId,
                'accounting_period_id' => $period->id,
            ]);

            $this->auditRepo->log('AR', 'POST', AccountsReceivableInvoice::class, $id, $invoice->invoice_number, null, ['status' => 'POSTED']);
            DB::commit();
            return ['status' => 'success', 'message' => 'AR invoice posted and journal entry created'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function cancel($id)
    {
        try {
            $invoice = $this->model->find($id);
            if (!$invoice) return ['status' => 'error', 'message' => 'Invoice not found'];
            if (in_array($invoice->status, ['PAID', 'PARTIALLY_PAID'])) {
                return ['status' => 'error', 'message' => 'Cannot cancel a paid invoice'];
            }
            $invoice->update(['status' => 'CANCELLED']);
            $this->auditRepo->log('AR', 'CANCEL', AccountsReceivableInvoice::class, $id, $invoice->invoice_number, null, ['status' => 'CANCELLED']);
            return ['status' => 'success', 'message' => 'AR invoice cancelled successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getOverdue()
    {
        return $this->model
            ->with('customer', 'currency')
            ->whereIn('status', ['POSTED', 'PARTIALLY_PAID'])
            ->whereDate('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
    }

    public function getAgeing()
    {
        $invoices = $this->model
            ->with('customer', 'currency')
            ->whereIn('status', ['POSTED', 'PARTIALLY_PAID', 'OVERDUE'])
            ->get();

        $ageing = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
        foreach ($invoices as $inv) {
            $days = $inv->due_date->diffInDays(now(), false);
            if ($days <= 0)      $ageing['current'] += $inv->balance_due;
            elseif ($days <= 30) $ageing['1_30']    += $inv->balance_due;
            elseif ($days <= 60) $ageing['31_60']   += $inv->balance_due;
            elseif ($days <= 90) $ageing['61_90']   += $inv->balance_due;
            else                 $ageing['over_90'] += $inv->balance_due;
        }
        return $ageing;
    }

    public function generateInvoiceNumber(): string
    {
        $year  = date('Y');
        $count = $this->model->whereYear('created_at', $year)->count() + 1;
        return 'ARI-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
