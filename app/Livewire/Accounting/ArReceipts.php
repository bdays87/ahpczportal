<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iarreceiptInterface;
use App\Interfaces\iarinvoiceInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\icustomerInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ArReceipts extends Component
{
    use Toast, WithPagination;

    public $breadcrumbs    = [];
    public $search;
    public $filterStatus   = '';
    public $modal          = false;
    public $viewModal      = false;
    public $customer_id;
    public $customer_name;
    public $currency_id;
    public $exchange_rate   = 1;
    public $receipt_date;
    public $payment_method  = 'CASH';
    public $amount          = 0;
    public $reference;
    public $ar_account_id;
    public $cash_gl_account_id;
    public $notes;
    public $allocations     = [];
    public $openInvoices    = [];
    public $selectedReceipt;

    protected $arRecRepo;
    protected $arInvRepo;
    protected $customerRepo;
    protected $coaRepo;
    protected $currencyRepo;

    public function boot(
        iarreceiptInterface $arRecRepo,
        iarinvoiceInterface $arInvRepo,
        icustomerInterface $customerRepo,
        ichartofaccountsInterface $coaRepo,
        icurrencyInterface $currencyRepo
    ) {
        $this->arRecRepo    = $arRecRepo;
        $this->arInvRepo    = $arInvRepo;
        $this->customerRepo = $customerRepo;
        $this->coaRepo      = $coaRepo;
        $this->currencyRepo = $currencyRepo;
    }

    public function mount()
    {
        $this->receipt_date = date('Y-m-d');
        $this->breadcrumbs  = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'AR Receipts'],
        ];
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $this->openInvoices = $this->arInvRepo->getAll(null, 'POSTED', $value)->items();
            $this->allocations  = collect($this->openInvoices)->map(fn($inv) => [
                'ar_invoice_id'   => $inv->id,
                'invoice_number'  => $inv->invoice_number,
                'balance_due'     => $inv->balance_due,
                'amount_allocated' => 0,
            ])->toArray();
        } else {
            $this->openInvoices = [];
            $this->allocations  = [];
        }
    }

    public function getTotalAllocated(): float
    {
        return collect($this->allocations)->sum('amount_allocated');
    }

    public function getReceipts()
    {
        return $this->arRecRepo->getAll($this->search, $this->filterStatus ?: null);
    }

    public function save()
    {
        $this->validate([
            'receipt_date'      => 'required|date',
            'currency_id'       => 'required',
            'amount'            => 'required|numeric|min:0.01',
            'ar_account_id'     => 'required',
            'cash_gl_account_id' => 'required',
        ]);

        $allocations = collect($this->allocations)
            ->filter(fn($a) => (float)$a['amount_allocated'] > 0)
            ->map(fn($a) => [
                'ar_invoice_id'    => $a['ar_invoice_id'],
                'amount_allocated' => (float)$a['amount_allocated'],
            ])->values()->toArray();

        $data = [
            'customer_id'       => $this->customer_id ?: null,
            'customer_name'     => $this->customer_name,
            'receipt_date'      => $this->receipt_date,
            'currency_id'       => $this->currency_id,
            'exchange_rate'     => $this->exchange_rate ?? 1,
            'payment_method'    => $this->payment_method,
            'amount'            => $this->amount,
            'reference'         => $this->reference,
            'ar_account_id'     => $this->ar_account_id,
            'cash_gl_account_id' => $this->cash_gl_account_id,
            'notes'             => $this->notes,
        ];

        $response = $this->arRecRepo->create($data, $allocations);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->resetForm();
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function post($id)
    {
        $response = $this->arRecRepo->post($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function cancel($id)
    {
        $response = $this->arRecRepo->cancel($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function viewReceipt($id)
    {
        $this->selectedReceipt = $this->arRecRepo->get($id);
        $this->viewModal       = true;
    }

    public function delete($id)
    {
        $response = $this->arRecRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['customer_id','customer_name','currency_id','exchange_rate','payment_method',
            'amount','reference','ar_account_id','cash_gl_account_id','notes','allocations','openInvoices']);
        $this->receipt_date  = date('Y-m-d');
        $this->exchange_rate = 1;
        $this->amount        = 0;
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
            ['key' => 'receipt_number',  'label' => 'Receipt #'],
            ['key' => 'customer_name',   'label' => 'Customer'],
            ['key' => 'receipt_date',    'label' => 'Date'],
            ['key' => 'payment_method',  'label' => 'Method'],
            ['key' => 'amount',          'label' => 'Amount'],
            ['key' => 'status',          'label' => 'Status'],
            ['key' => 'action',          'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.ar-receipts', [
            'receipts'       => $this->getReceipts(),
            'headers'        => $this->headers(),
            'customers'      => $this->getFormattedCustomers(),
            'currencies'     => $this->currencyRepo->getAll('active'),
            'arAccounts'     => $this->coaRepo->getByType('ASSET'),
            'bankAccounts'   => $this->coaRepo->getByType('ASSET'),
            'unpaidInvoices' => $this->openInvoices,
            'totalAllocated' => $this->getTotalAllocated(),
        ]);
    }
}
