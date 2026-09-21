<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iapinvoiceInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icostcenterInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\isupplierInterface;
use App\Interfaces\itaxrateInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ApInvoices extends Component
{
    use Toast, WithPagination;

    public $breadcrumbs  = [];
    public $search;
    public $filterStatus = '';
    public $modal        = false;
    public $viewModal    = false;
    public $id;
    public $supplier_id;
    public $supplier_reference;
    public $invoice_date;
    public $due_date;
    public $description;
    public $currency_id;
    public $exchange_rate  = 1;
    public $subtotal       = 0;
    public $tax_amount     = 0;
    public $discount_amount = 0;
    public $total_amount   = 0;
    public $expense_account_id;
    public $ap_account_id;
    public $cost_center_id;
    public $tax_rate_id;
    public $notes;
    public $selectedInvoice;

    protected $apRepo;
    protected $supplierRepo;
    protected $coaRepo;
    protected $ccRepo;
    protected $currencyRepo;
    protected $taxRepo;

    public function boot(
        iapinvoiceInterface $apRepo,
        isupplierInterface $supplierRepo,
        ichartofaccountsInterface $coaRepo,
        icostcenterInterface $ccRepo,
        icurrencyInterface $currencyRepo,
        itaxrateInterface $taxRepo
    ) {
        $this->apRepo       = $apRepo;
        $this->supplierRepo = $supplierRepo;
        $this->coaRepo      = $coaRepo;
        $this->ccRepo       = $ccRepo;
        $this->currencyRepo = $currencyRepo;
        $this->taxRepo      = $taxRepo;
    }

    public function mount()
    {
        $this->invoice_date = date('Y-m-d');
        $this->due_date     = date('Y-m-d', strtotime('+30 days'));
        $this->breadcrumbs  = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'AP Invoices'],
        ];
    }

    public function updatedSubtotal()   { $this->recalculate(); }
    public function updatedTaxAmount()  { $this->recalculate(); }
    public function updatedDiscountAmount() { $this->recalculate(); }

    public function updatedTaxRateId($value)
    {
        if ($value) {
            $rate            = $this->taxRepo->get($value);
            $this->tax_amount = round($this->subtotal * $rate->rate / 100, 2);
            $this->recalculate();
        }
    }

    private function recalculate()
    {
        $this->total_amount = round(
            (float)$this->subtotal + (float)$this->tax_amount - (float)$this->discount_amount,
            2
        );
    }

    public function getInvoices()
    {
        return $this->apRepo->getAll($this->search, $this->filterStatus ?: null);
    }

    public function save()
    {
        $this->validate([
            'supplier_id'        => 'required',
            'invoice_date'       => 'required|date',
            'due_date'           => 'required|date|after_or_equal:invoice_date',
            'currency_id'        => 'required',
            'subtotal'           => 'required|numeric|min:0.01',
            'expense_account_id' => 'required',
            'ap_account_id'      => 'required',
        ]);

        $data = [
            'supplier_id'        => $this->supplier_id,
            'supplier_reference' => $this->supplier_reference,
            'invoice_date'       => $this->invoice_date,
            'due_date'           => $this->due_date,
            'description'        => $this->description,
            'currency_id'        => $this->currency_id,
            'exchange_rate'      => $this->exchange_rate ?? 1,
            'subtotal'           => $this->subtotal,
            'tax_amount'         => $this->tax_amount ?? 0,
            'discount_amount'    => $this->discount_amount ?? 0,
            'total_amount'       => $this->total_amount,
            'expense_account_id' => $this->expense_account_id,
            'ap_account_id'      => $this->ap_account_id,
            'cost_center_id'     => $this->cost_center_id ?: null,
            'tax_rate_id'        => $this->tax_rate_id ?: null,
            'notes'              => $this->notes,
        ];

        $response = $this->id
            ? $this->apRepo->update($this->id, $data)
            : $this->apRepo->create($data);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->resetForm();
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $inv                     = $this->apRepo->get($id);
        $this->id                = $inv->id;
        $this->supplier_id       = $inv->supplier_id;
        $this->supplier_reference = $inv->supplier_reference;
        $this->invoice_date      = $inv->invoice_date->format('Y-m-d');
        $this->due_date          = $inv->due_date->format('Y-m-d');
        $this->description       = $inv->description;
        $this->currency_id       = $inv->currency_id;
        $this->exchange_rate     = $inv->exchange_rate;
        $this->subtotal          = $inv->subtotal;
        $this->tax_amount        = $inv->tax_amount;
        $this->discount_amount   = $inv->discount_amount;
        $this->total_amount      = $inv->total_amount;
        $this->expense_account_id = $inv->expense_account_id;
        $this->ap_account_id     = $inv->ap_account_id;
        $this->cost_center_id    = $inv->cost_center_id;
        $this->tax_rate_id       = $inv->tax_rate_id;
        $this->notes             = $inv->notes;
        $this->modal             = true;
    }

    public function post($id)
    {
        $response = $this->apRepo->post($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function cancel($id)
    {
        $response = $this->apRepo->cancel($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function viewInvoice($id)
    {
        $this->selectedInvoice = $this->apRepo->get($id);
        $this->viewModal       = true;
    }

    public function delete($id)
    {
        $response = $this->apRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['id','supplier_id','supplier_reference','description','currency_id','exchange_rate',
            'subtotal','tax_amount','discount_amount','total_amount','expense_account_id','ap_account_id',
            'cost_center_id','tax_rate_id','notes']);
        $this->invoice_date = date('Y-m-d');
        $this->due_date     = date('Y-m-d', strtotime('+30 days'));
        $this->exchange_rate = 1;
    }

    public function headers(): array
    {
        return [
            ['key' => 'invoice_number',     'label' => 'Invoice #'],
            ['key' => 'supplier.name',       'label' => 'Supplier'],
            ['key' => 'invoice_date',        'label' => 'Date'],
            ['key' => 'due_date',            'label' => 'Due Date'],
            ['key' => 'total_amount',        'label' => 'Total'],
            ['key' => 'balance_due',         'label' => 'Balance'],
            ['key' => 'status',              'label' => 'Status'],
            ['key' => 'action',              'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.ap-invoices', [
            'invoices'       => $this->getInvoices(),
            'headers'        => $this->headers(),
            'suppliers'      => $this->supplierRepo->getAll(null, 'active'),
            'currencies'     => $this->currencyRepo->getAll('active'),
            'expenseAccounts' => $this->coaRepo->getByType('EXPENSE'),
            'apAccounts'     => $this->coaRepo->getByType('LIABILITY'),
            'costCenters'    => $this->ccRepo->getAll('active'),
            'taxRates'       => $this->taxRepo->getAll('active'),
            'ageingData'     => $this->apRepo->getAgeing(),
        ]);
    }
}
