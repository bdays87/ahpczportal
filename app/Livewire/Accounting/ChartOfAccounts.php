<?php

namespace App\Livewire\Accounting;

use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icostcenterInterface;
use App\Interfaces\icurrencyInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class ChartOfAccounts extends Component
{
    use Toast;

    public $breadcrumbs = [];
    public $search;
    public $filterType;
    public $modal       = false;
    public $ledgerModal = false;
    public $id;
    public $code;
    public $name;
    public $description;
    public $account_type  = 'ASSET';
    public $account_subtype;
    public $parent_id;
    public $cost_center_id;
    public $currency_id;
    public $normal_balance = 'DEBIT';
    public $is_header      = false;
    public $allow_direct_posting = true;
    public $status         = 'active';
    public $selectedAccount;
    public $ledgerFrom;
    public $ledgerTo;

    protected $coaRepo;
    protected $ccRepo;
    protected $currencyRepo;

    public function boot(ichartofaccountsInterface $coaRepo, icostcenterInterface $ccRepo, icurrencyInterface $currencyRepo)
    {
        $this->coaRepo      = $coaRepo;
        $this->ccRepo       = $ccRepo;
        $this->currencyRepo = $currencyRepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard',  'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Chart of Accounts'],
        ];
    }

    public function updatedAccountType($value)
    {
        $this->normal_balance = in_array($value, ['ASSET', 'EXPENSE']) ? 'DEBIT' : 'CREDIT';
    }

    public function getAccounts()
    {
        return $this->coaRepo->getAll($this->search, $this->filterType ?: null, 'active');
    }

    public function getParentAccounts()
    {
        return $this->coaRepo->getAll(null, null, 'active');
    }

    public function getCostCenters()
    {
        return $this->ccRepo->getAll('active');
    }

    public function getCurrencies()
    {
        return $this->currencyRepo->getAll('active');
    }

    public function save()
    {
        $this->validate([
            'code'         => 'required|string|max:20',
            'name'         => 'required|string|max:150',
            'account_type' => 'required|in:ASSET,LIABILITY,EQUITY,INCOME,EXPENSE',
        ]);

        $data = [
            'code'                => $this->code,
            'name'                => $this->name,
            'description'         => $this->description,
            'account_type'        => $this->account_type,
            'account_subtype'     => $this->account_subtype,
            'parent_id'           => $this->parent_id ?: null,
            'cost_center_id'      => $this->cost_center_id ?: null,
            'currency_id'         => $this->currency_id ?: null,
            'normal_balance'      => $this->normal_balance,
            'is_header'           => (bool) $this->is_header,
            'allow_direct_posting' => (bool) $this->allow_direct_posting,
            'status'              => $this->status,
        ];

        $response = $this->id
            ? $this->coaRepo->update($this->id, $data)
            : $this->coaRepo->create($data);

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
        $account                    = $this->coaRepo->get($id);
        $this->id                   = $account->id;
        $this->code                 = $account->code;
        $this->name                 = $account->name;
        $this->description          = $account->description;
        $this->account_type         = $account->account_type;
        $this->account_subtype      = $account->account_subtype;
        $this->parent_id            = $account->parent_id;
        $this->cost_center_id       = $account->cost_center_id;
        $this->currency_id          = $account->currency_id;
        $this->normal_balance        = $account->normal_balance;
        $this->is_header            = $account->is_header;
        $this->allow_direct_posting = $account->allow_direct_posting;
        $this->status               = $account->status;
        $this->modal                = true;
    }

    public function delete($id)
    {
        $response = $this->coaRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function viewLedger($id)
    {
        $this->selectedAccount = $this->coaRepo->get($id);
        $this->ledgerModal     = true;
    }

    public function getLedgerEntries()
    {
        if (!$this->selectedAccount) return collect();
        return $this->coaRepo->getLedger($this->selectedAccount->id, null, $this->ledgerFrom, $this->ledgerTo);
    }

    private function resetForm()
    {
        $this->reset(['id', 'code', 'name', 'description', 'account_type', 'account_subtype', 'parent_id', 'cost_center_id', 'currency_id', 'is_header', 'allow_direct_posting']);
        $this->normal_balance = 'DEBIT';
        $this->status         = 'active';
    }

    public function headers(): array
    {
        return [
            ['key' => 'code',         'label' => 'Code'],
            ['key' => 'name',         'label' => 'Account Name'],
            ['key' => 'account_type', 'label' => 'Type'],
            ['key' => 'account_subtype', 'label' => 'Sub-type'],
            ['key' => 'normal_balance', 'label' => 'Dr/Cr'],
            ['key' => 'status',       'label' => 'Status'],
            ['key' => 'action',       'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.chart-of-accounts', [
            'accounts'     => $this->getAccounts(),
            'headers'      => $this->headers(),
            'parentAccounts' => $this->getParentAccounts(),
            'costCenters'  => $this->getCostCenters(),
            'currencies'   => $this->getCurrencies(),
            'ledgerEntries' => $this->ledgerModal ? $this->getLedgerEntries() : collect(),
        ]);
    }
}
