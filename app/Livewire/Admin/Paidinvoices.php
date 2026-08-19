<?php

namespace App\Livewire\Admin;

use App\Interfaces\icurrencyInterface;
use App\Interfaces\invoiceInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Paidinvoices extends Component
{
    use Toast, WithPagination;

    public $search;

    public $year;

    public $currency_id;

    public $breadcrumbs = [];

    public $invoice = null;

    public bool $modal = false;

    public bool $duplicatesmodal = false;

    public $duplicategroups = [];

    protected $invoicerepo;

    protected $currencyrepo;

    public function boot(invoiceInterface $invoicerepo, icurrencyInterface $currencyrepo)
    {
        $this->invoicerepo = $invoicerepo;
        $this->currencyrepo = $currencyrepo;
    }

    public function mount()
    {
        $this->year = (string) date('Y');

        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Finance',
            ],
            [
                'label' => 'Paid Invoices',
            ],
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedYear()
    {
        $this->resetPage();
    }

    public function updatedCurrencyId()
    {
        $this->resetPage();
    }

    public function getyears()
    {
        $years = $this->invoicerepo->getpaidinvoiceyears();

        if (! $years->contains(date('Y'))) {
            $years->prepend(date('Y'));
        }

        return $years->map(fn ($year) => ['id' => (string) $year, 'name' => (string) $year]);
    }

    public function getcurrencies()
    {
        return $this->currencyrepo->getAll('active');
    }

    public function getinvoices()
    {
        return $this->invoicerepo->getpaidinvoices($this->year, $this->search, $this->currency_id);
    }

    public function gettotals()
    {
        $rawtotals = $this->invoicerepo->getpaidinvoicetotals($this->year, $this->search, $this->currency_id);

        // Show every active currency, including ones with nothing paid in the
        // current filter (as a plain 0), rather than only the currencies that
        // happen to have a matching invoice.
        return $this->getcurrencies()->map(function ($currency) use ($rawtotals) {
            $match = $rawtotals->firstWhere('currency_id', $currency->id);

            return (object) [
                'currency' => $currency,
                'total_amount' => $match->total_amount ?? 0,
                'invoice_count' => $match->invoice_count ?? 0,
            ];
        });
    }

    public function view($id)
    {
        $this->invoice = $this->invoicerepo->getInvoice($id);
        $this->modal = true;
    }

    public function deletereceipt($id)
    {
        $response = $this->invoicerepo->deletereceipt($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            // refresh whatever is currently open so the deleted row disappears
            if ($this->invoice) {
                $this->invoice = $this->invoicerepo->getInvoice($this->invoice->id);
            }
            $this->scanduplicates(silent: true);
        } else {
            $this->error($response['message']);
        }
    }

    /** Look for receipts sharing the same receipt_number and open the review modal. */
    public function scanduplicates($silent = false)
    {
        $groups = $this->invoicerepo->findduplicatereceipts();

        // Flattened to plain arrays — keeping raw Eloquent collections in a
        // public Livewire property is asking for hydration trouble.
        $this->duplicategroups = $groups->map(function ($receipts, $receiptnumber) {
            $first = $receipts->first();
            $keepid = $receipts->sortBy('id')->first()->id;

            return [
                'receipt_number' => $receiptnumber,
                'invoice_number' => $first->invoice->invoice_number ?? 'N/A',
                'invoice_amount' => $first->invoice->amount ?? 0,
                'currency' => $first->currency->name ?? '',
                'customer' => trim(($first->customer->name ?? '').' '.($first->customer->surname ?? '')),
                'receipts' => $receipts->map(fn ($r) => [
                    'id' => $r->id,
                    'amount' => $r->amount,
                    'created_at' => $r->created_at->format('d M Y H:i'),
                    'keep' => $r->id === $keepid,
                ])->values()->all(),
            ];
        })->values()->all();

        if (! $silent) {
            if (empty($this->duplicategroups)) {
                $this->success('No duplicate receipts found.');
            } else {
                $this->duplicatesmodal = true;
            }
        }
    }

    /** Delete every extra receipt found by the scan, keeping the earliest of each group. */
    public function removeduplicates()
    {
        $response = $this->invoicerepo->deleteduplicatereceipts();
        $this->success($response['message']);
        $this->scanduplicates(silent: true);

        if (empty($this->duplicategroups)) {
            $this->duplicatesmodal = false;
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'created_at', 'label' => 'Date'],
            ['key' => 'invoice_number', 'label' => 'Invoice No', 'class' => 'whitespace-normal break-words'],
            ['key' => 'customer', 'label' => 'Customer', 'class' => 'whitespace-normal break-words'],
            ['key' => 'description', 'label' => 'Description', 'class' => 'whitespace-normal break-words max-w-[16rem]'],
            ['key' => 'amount', 'label' => 'Amount'],
            ['key' => 'receipt_number', 'label' => 'Receipt No', 'class' => 'whitespace-normal break-words'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.paidinvoices', [
            'invoices' => $this->getinvoices(),
            'headers' => $this->headers(),
            'years' => $this->getyears(),
            'currencies' => $this->getcurrencies(),
            'totals' => $this->gettotals(),
        ]);
    }
}
