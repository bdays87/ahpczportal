<?php

namespace App\Livewire\Accounting;

use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\isupplierInterface;
use App\Interfaces\itaxrateInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Suppliers extends Component
{
    use Toast;

    public $breadcrumbs = [];
    public $search;
    public $modal        = false;
    public $viewModal    = false;
    public $id;
    public $code;
    public $name;
    public $contact_person;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $country;
    public $tax_number;
    public $currency_id;
    public $ap_account_id;
    public $tax_rate_id;
    public $credit_limit   = 0;
    public $payment_terms  = 30;
    public $bank_name;
    public $bank_account_number;
    public $bank_branch;
    public $status         = 'active';
    public $selectedSupplier;

    protected $supplierRepo;
    protected $coaRepo;
    protected $currencyRepo;
    protected $taxRepo;

    public function boot(
        isupplierInterface $supplierRepo,
        ichartofaccountsInterface $coaRepo,
        icurrencyInterface $currencyRepo,
        itaxrateInterface $taxRepo
    ) {
        $this->supplierRepo = $supplierRepo;
        $this->coaRepo      = $coaRepo;
        $this->currencyRepo = $currencyRepo;
        $this->taxRepo      = $taxRepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Suppliers'],
        ];
    }

    public function getSuppliers()
    {
        return $this->supplierRepo->getAll($this->search, 'active');
    }

    public function save()
    {
        $this->validate([
            'name'        => 'required|string|max:200',
            'currency_id' => 'required',
        ]);

        $data = [
            'code'                => $this->code,
            'name'                => $this->name,
            'contact_person'      => $this->contact_person,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'address'             => $this->address,
            'city'                => $this->city,
            'country'             => $this->country,
            'tax_number'          => $this->tax_number,
            'currency_id'         => $this->currency_id,
            'ap_account_id'       => $this->ap_account_id ?: null,
            'tax_rate_id'         => $this->tax_rate_id ?: null,
            'credit_limit'        => $this->credit_limit ?? 0,
            'payment_terms'       => $this->payment_terms ?? 30,
            'bank_name'           => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_branch'         => $this->bank_branch,
            'status'              => $this->status,
        ];

        $response = $this->id
            ? $this->supplierRepo->update($this->id, $data)
            : $this->supplierRepo->create($data);

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
        $s                     = $this->supplierRepo->get($id);
        $this->id              = $s->id;
        $this->code            = $s->code;
        $this->name            = $s->name;
        $this->contact_person  = $s->contact_person;
        $this->email           = $s->email;
        $this->phone           = $s->phone;
        $this->address         = $s->address;
        $this->city            = $s->city;
        $this->country         = $s->country;
        $this->tax_number      = $s->tax_number;
        $this->currency_id     = $s->currency_id;
        $this->ap_account_id   = $s->ap_account_id;
        $this->tax_rate_id     = $s->tax_rate_id;
        $this->credit_limit    = $s->credit_limit;
        $this->payment_terms   = $s->payment_terms;
        $this->bank_name       = $s->bank_name;
        $this->bank_account_number = $s->bank_account_number;
        $this->bank_branch     = $s->bank_branch;
        $this->status          = $s->status;
        $this->modal           = true;
    }

    public function viewSupplier($id)
    {
        $this->selectedSupplier = $this->supplierRepo->getStatement($id);
        $this->viewModal        = true;
    }

    public function delete($id)
    {
        $response = $this->supplierRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['id','code','name','contact_person','email','phone','address','city','country',
            'tax_number','currency_id','ap_account_id','tax_rate_id','credit_limit','payment_terms',
            'bank_name','bank_account_number','bank_branch']);
        $this->status        = 'active';
        $this->payment_terms = 30;
        $this->credit_limit  = 0;
    }

    public function headers(): array
    {
        return [
            ['key' => 'code',           'label' => 'Code'],
            ['key' => 'name',           'label' => 'Supplier Name'],
            ['key' => 'email',          'label' => 'Email'],
            ['key' => 'phone',          'label' => 'Phone'],
            ['key' => 'payment_terms',  'label' => 'Terms (Days)'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'action',         'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.suppliers', [
            'suppliers'   => $this->getSuppliers(),
            'headers'     => $this->headers(),
            'currencies'  => $this->currencyRepo->getAll('active'),
            'apAccounts'  => $this->coaRepo->getByType('LIABILITY'),
            'taxRates'    => $this->taxRepo->getAll('active'),
        ]);
    }
}
