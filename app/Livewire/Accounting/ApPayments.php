<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iappaymentInterface;
use App\Interfaces\iapinvoiceInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\isupplierInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ApPayments extends Component
{
    use Toast, WithPagination;

    public $breadcrumbs   = [];
    public $search;
    public $filterStatus  = '';
    public $modal         = false;
    public $viewModal     = false;
    public $supplier_id;
    public $currency_id;
    public $exchange_rate  = 1;
    public $payment_date;
    public $payment_method = 'BANK_TRANSFER';
    public $amount         = 0;
    public $reference;
    public $ap_account_id;
    public $bank_gl_account_id;
    public $notes;
    public $allocations    = [];
    public $selectedPayment;
    public $openInvoices   = [];

    protected $apPayRepo;
    protected $apInvRepo;
    protected $supplierRepo;
    protected $coaRepo;
    protected $currencyRepo;

    public function boot(
        iappaymentInterface $apPayRepo,
        iapinvoiceInterface $apInvRepo,
        isupplierInterface $supplierRepo,
        ichartofaccountsInterface $coaRepo,
        icurrencyInterface $currencyRepo
    ) {
        $this->apPayRepo    = $apPayRepo;
        $this->apInvRepo    = $apInvRepo;
        $this->supplierRepo = $supplierRepo;
        $this->coaRepo      = $coaRepo;
        $this->currencyRepo = $currencyRepo;
    }

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
        $this->breadcrumbs  = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'AP Payments'],
        ];
    }

    public function updatedSupplierId($value)
    {
        if ($value) {
            $this->openInvoices = $this->apInvRepo->getAll(null, 'POSTED', $value)->items();
            $supplier           = $this->supplierRepo->get($value);
            if ($supplier && $supplier->ap_account_id) {
                $this->ap_account_id = $supplier->ap_account_id;
            }
            $this->allocations = collect($this->openInvoices)->map(fn($inv) => [
                'ap_invoice_id'   => $inv->id,
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

    public function getPayments()
    {
        return $this->apPayRepo->getAll($this->search, $this->filterStatus ?: null);
    }

    public function save()
    {
        $this->validate([
            'supplier_id'       => 'required',
            'payment_date'      => 'required|date',
            'currency_id'       => 'required',
            'amount'            => 'required|numeric|min:0.01',
            'ap_account_id'     => 'required',
            'bank_gl_account_id' => 'required',
        ]);

        $allocations = collect($this->allocations)
            ->filter(fn($a) => (float)$a['amount_allocated'] > 0)
            ->map(fn($a) => [
                'ap_invoice_id'   => $a['ap_invoice_id'],
                'amount_allocated' => (float)$a['amount_allocated'],
            ])->values()->toArray();

        $data = [
            'supplier_id'       => $this->supplier_id,
            'payment_date'      => $this->payment_date,
            'currency_id'       => $this->currency_id,
            'exchange_rate'     => $this->exchange_rate ?? 1,
            'payment_method'    => $this->payment_method,
            'amount'            => $this->amount,
            'reference'         => $this->reference,
            'ap_account_id'     => $this->ap_account_id,
            'bank_gl_account_id' => $this->bank_gl_account_id,
            'notes'             => $this->notes,
        ];

        $response = $this->apPayRepo->create($data, $allocations);

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
        $response = $this->apPayRepo->post($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function cancel($id)
    {
        $response = $this->apPayRepo->cancel($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function viewPayment($id)
    {
        $this->selectedPayment = $this->apPayRepo->get($id);
        $this->viewModal       = true;
    }

    public function delete($id)
    {
        $response = $this->apPayRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['supplier_id','currency_id','exchange_rate','payment_method','amount',
            'reference','ap_account_id','bank_gl_account_id','notes','allocations','openInvoices']);
        $this->payment_date  = date('Y-m-d');
        $this->exchange_rate = 1;
        $this->amount        = 0;
    }

    public function headers(): array
    {
        return [
            ['key' => 'payment_number',  'label' => 'Payment #'],
            ['key' => 'supplier.name',   'label' => 'Supplier'],
            ['key' => 'payment_date',    'label' => 'Date'],
            ['key' => 'payment_method',  'label' => 'Method'],
            ['key' => 'amount',          'label' => 'Amount'],
            ['key' => 'status',          'label' => 'Status'],
            ['key' => 'action',          'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.ap-payments', [
            'payments'       => $this->getPayments(),
            'headers'        => $this->headers(),
            'suppliers'      => $this->supplierRepo->getAll(null, 'active'),
            'currencies'     => $this->currencyRepo->getAll('active'),
            'apAccounts'     => $this->coaRepo->getByType('LIABILITY'),
            'bankAccounts'   => $this->coaRepo->getByType('ASSET'),
            'unpaidInvoices' => $this->openInvoices,
            'totalAllocated' => $this->getTotalAllocated(),
        ]);
    }
}
