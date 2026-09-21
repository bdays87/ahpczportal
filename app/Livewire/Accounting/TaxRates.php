<?php

namespace App\Livewire\Accounting;

use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\itaxrateInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class TaxRates extends Component
{
    use Toast;

    public $breadcrumbs  = [];
    public $modal        = false;
    public $reportModal  = false;
    public $id;
    public $name;
    public $code;
    public $tax_type     = 'VAT';
    public $rate         = 0;
    public $sales_account_id;
    public $purchase_account_id;
    public $status       = 'active';
    public $reportFrom;
    public $reportTo;
    public $reportData   = [];

    protected $taxRepo;
    protected $coaRepo;

    public function boot(itaxrateInterface $taxRepo, ichartofaccountsInterface $coaRepo)
    {
        $this->taxRepo = $taxRepo;
        $this->coaRepo = $coaRepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Tax Rates'],
        ];
    }

    public function getTaxRates()
    {
        return $this->taxRepo->getAll();
    }

    public function save()
    {
        $this->validate([
            'name'     => 'required|string|max:100',
            'code'     => 'required|string|max:20',
            'tax_type' => 'required|in:VAT,WITHHOLDING,INCOME,EXEMPT',
            'rate'     => 'required|numeric|min:0|max:100',
        ]);

        $data = [
            'name'                => $this->name,
            'code'                => $this->code,
            'tax_type'            => $this->tax_type,
            'rate'                => $this->rate,
            'sales_account_id'    => $this->sales_account_id ?: null,
            'purchase_account_id' => $this->purchase_account_id ?: null,
            'status'              => $this->status,
        ];

        $response = $this->id
            ? $this->taxRepo->update($this->id, $data)
            : $this->taxRepo->create($data);

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
        $t                       = $this->taxRepo->get($id);
        $this->id                = $t->id;
        $this->name              = $t->name;
        $this->code              = $t->code;
        $this->tax_type          = $t->tax_type;
        $this->rate              = $t->rate;
        $this->sales_account_id  = $t->sales_account_id;
        $this->purchase_account_id = $t->purchase_account_id;
        $this->status            = $t->status;
        $this->modal             = true;
    }

    public function delete($id)
    {
        $response = $this->taxRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function runReport()
    {
        $this->reportData = $this->taxRepo->getTaxReport(null, $this->reportFrom, $this->reportTo);
        $this->reportModal = true;
    }

    private function resetForm()
    {
        $this->reset(['id', 'name', 'code', 'rate', 'sales_account_id', 'purchase_account_id']);
        $this->tax_type = 'VAT';
        $this->status   = 'active';
    }

    public function headers(): array
    {
        return [
            ['key' => 'name',           'label' => 'Name'],
            ['key' => 'code',           'label' => 'Code'],
            ['key' => 'tax_type',       'label' => 'Type'],
            ['key' => 'rate',           'label' => 'Rate (%)'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'action',         'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.tax-rates', [
            'taxRates'   => $this->getTaxRates(),
            'headers'    => $this->headers(),
            'glAccounts' => $this->coaRepo->getPostable(),
        ]);
    }
}
