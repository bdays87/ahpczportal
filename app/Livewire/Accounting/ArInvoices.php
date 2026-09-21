<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iarinvoiceInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icostcenterInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomerInterface;
use App\Interfaces\itaxrateInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ArInvoices extends Component
{
    use Toast, WithPagination;

    public $breadcrumbs   = [];
    public $search;
    public $filterStatus  = '';
    public $modal         = false;
    public $viewModal     = false;
    public $id;
    public $customer_id;
    public $customer_name;
    public $customer_email;
    public $invoice_date;
    public $due_date;
    public $description;
    public $currency_id;
    public $exchange_rate  = 1;
    public $subtotal       = 0;
    public $tax_amount     = 0;
    public $discount_amount = 0;
    public $total_amount   = 0;
    public $revenue_account_id;
    public $ar_account_id;
    public $cost_center_id;
    public $tax_rate_id;
    public $notes;
    public $selectedInvoice;

    protected $arRepo;
    protected $customerRepo;
    protected $coaRepo;
    protected $ccRepo;
    protected $currencyRepo;
    protected $taxRepo;

    public function boot(
        iarinvoiceInterface $arRepo,
        icustomerInterface $customerRepo,
        ichartofaccountsInterface $coaRepo,
        icostcenterInterface $ccRepo,
        icurrencyInterface $currencyRepo,
        itaxrateInterface $taxRepo
    ) {
        $this->arRepo       = $arRepo;
        $this->customerRepo = $customerRepo;
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
            ['label' => 'AR Invoices'],
        ];
    }

    public function updatedSubtotal()       { $this->recalculate(); }
    public function updatedTaxAmount()      { $this->recalculate(); }
    public function updatedDiscountAmount() { $this->recalculate(); }

    public function updatedTaxRateId($value)
    {
        if ($value) {
            $rate             = $this->taxRepo->get($value);
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
        return $this->arRepo->getAll($this->search, $this->filterStatus ?: null);
    }

    public function save()
    {
        $this->validate([
            'invoice_date'       => 'required|date',
            'due_date'           => 'required|date|after_or_equal:invoice_date',
            'currency_id'        => 'required',
            'subtotal'           => 'required|numeric|min:0.01',
            'revenue_account_id' => 'required',
            'ar_account_id'      => 'required',
        ]);

        $data = [
            'customer_id'       => $this->customer_id ?: null,
            'customer_name'     => $this->customer_name,
            'customer_email'    => $this->customer_email,
            'invoice_date'      => $this->invoice_date,
            'due_date'          => $this->due_date,
            'description'       => $this->description,
            'currency_id'       => $this->currency_id,
            'exchange_rate'     => $this->exchange_rate ?? 1,
            'subtotal'          => $this->subtotal,
            'tax_amount'        => $this->tax_amount ?? 0,
            'discount_amount'   => $this->discount_amount ?? 0,
            'total_amount'      => $this->total_amount,
            'revenue_account_id' => $this->revenue_account_id,
            'ar_account_id'     => $this->ar_account_id,
            'cost_center_id'    => $this->cost_center_id ?: null,
            'tax_rate_id'       => $this->tax_rate_id ?: null,
            'notes'             => $this->notes,
        ];

        $response = $this->id
            ? $this->arRepo->update($this->id, $data)
            : $this->arRepo->create($data);

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
        $inv                   = $this->arRepo->get($id);
        $this->id              = $inv->id;
        $this->customer_id     = $inv->customer_id;
        $this->customer_name   = $inv->customer_name;
        $this->customer_email  = $inv->customer_email;
        $this->invoice_date    = $inv->invoice_date->format('Y-m-d');
        $this->due_date        = $inv->due_date->format('Y-m-d');
        $this->description     = $inv->description;
        $this->currency_id     = $inv->currency_id;
        $this->exchange_rate   = $inv->exchange_rate;
        $this->subtotal        = $inv->subtotal;
        $this->tax_amount      = $inv->tax_amount;
        $this->discount_amount = $inv->discount_amount;
        $this->total_amount    = $inv->total_amount;
        $this->revenue_account_id = $inv->revenue_account_id;
        $this->ar_account_id   = $inv->ar_account_id;
        $this->cost_center_id  = $inv->cost_center_id;
        $this->tax_rate_id     = $inv->tax_rate_id;
        $this->notes           = $inv->notes;
        $this->modal           = true;
    }

    public function post($id)
    {
        $response = $this->arRepo->post($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function cancel($id)
    {
        $response = $this->arRepo->cancel($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function viewInvoice($id)
    {
        $this->selectedInvoice = $this->arRepo->get($id);
        $this->viewModal       = true;
    }

    public function delete($id)
    {
        $response = $this->arRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['id','customer_id','customer_name','customer_email','description','currency_id',
            'exchange_rate','subtotal','tax_amount','discount_amount','total_amount',
            'revenue_account_id','ar_account_id','cost_center_id','tax_rate_id','notes']);
        $this->invoice_date  = date('Y-m-d');
        $this->due_date      = date('Y-m-d', strtotime('+30 days'));
        $this->exchange_rate = 1;
    }

    private function getFormattedCustomers()
    {
        return $this->customerRepo->getallsearch('', [])->map(function ($customer) {
            // Get registration number from first active profession
            $regNumber = $customer->customerprofessions->where('status', 'active')->first()?->registrationnumber ?? 'N/A';
            
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'surname' => $customer->surname,
                'full_name' => $customer->name . ' ' . $customer->surname,
                'reg_number' => $regNumber,
                'display_name' => $customer->name . ' ' . $customer->surname . ' (' . $regNumber . ')',
                'search_text' => strtolower($customer->name . ' ' . $customer->surname . ' ' . $regNumber),
            ];
        });
    }

    public function headers(): array
    {
        return [
            ['key' => 'invoice_number',  'label' => 'Invoice #'],
            ['key' => 'customer_name',   'label' => 'Customer'],
            ['key' => 'invoice_date',    'label' => 'Date'],
            ['key' => 'due_date',        'label' => 'Due Date'],
            ['key' => 'total_amount',    'label' => 'Total'],
            ['key' => 'balance_due',     'label' => 'Balance'],
            ['key' => 'status',          'label' => 'Status'],
            ['key' => 'action',          'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.ar-invoices', [
            'invoices'        => $this->getInvoices(),
            'headers'         => $this->headers(),
            'customers'       => $this->getFormattedCustomers(),
            'currencies'      => $this->currencyRepo->getAll('active'),
            'revenueAccounts' => $this->coaRepo->getByType('REVENUE'),
            'arAccounts'      => $this->coaRepo->getByType('ASSET'),
            'costCenters'     => $this->ccRepo->getAll('active'),
            'taxRates'        => $this->taxRepo->getAll('active'),
            'ageingData'      => $this->arRepo->getAgeing(),
        ]);
    }
}
