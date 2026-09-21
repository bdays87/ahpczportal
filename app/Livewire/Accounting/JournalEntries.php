<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iaccountingperiodInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icostcenterInterface;
use App\Interfaces\icurrencyInterface;
use App\Interfaces\ijournalentryInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class JournalEntries extends Component
{
    use Toast, WithPagination;

    public $breadcrumbs  = [];
    public $search;
    public $filterStatus = 'DRAFT';
    public $filterPeriod;
    public $modal        = false;
    public $viewModal    = false;
    public $reverseModal = false;
    public $id;
    public $entry_date;
    public $description;
    public $narration;
    public $entry_type   = 'MANUAL';
    public $currency_id;
    public $exchange_rate = 1;
    public $reverseReason;
    public $selectedEntry;

    // Journal lines (array of rows)
    public $lines = [];

    protected $journalRepo;
    protected $coaRepo;
    protected $ccRepo;
    protected $periodRepo;
    protected $currencyRepo;

    public function boot(
        ijournalentryInterface $journalRepo,
        ichartofaccountsInterface $coaRepo,
        icostcenterInterface $ccRepo,
        iaccountingperiodInterface $periodRepo,
        icurrencyInterface $currencyRepo
    ) {
        $this->journalRepo  = $journalRepo;
        $this->coaRepo      = $coaRepo;
        $this->ccRepo       = $ccRepo;
        $this->periodRepo   = $periodRepo;
        $this->currencyRepo = $currencyRepo;
    }

    public function mount()
    {
        $this->entry_date = date('Y-m-d');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Journal Entries'],
        ];
        $this->addLine();
        $this->addLine();
    }

    public function getEntries()
    {
        return $this->journalRepo->getAll($this->search, $this->filterStatus ?: null, $this->filterPeriod ?: null);
    }

    public function addLine()
    {
        $this->lines[] = [
            'account_id'     => null,
            'cost_center_id' => null,
            'description'    => '',
            'debit'          => 0,
            'credit'         => 0,
        ];
    }

    public function removeLine($index)
    {
        if (count($this->lines) <= 2) {
            $this->error('A journal entry must have at least two lines');
            return;
        }
        array_splice($this->lines, $index, 1);
        $this->lines = array_values($this->lines);
    }

    public function getTotals(): array
    {
        $debit  = collect($this->lines)->sum('debit');
        $credit = collect($this->lines)->sum('credit');
        return [
            'debit'    => $debit,
            'credit'   => $credit,
            'balanced' => round((float) $debit, 2) === round((float) $credit, 2),
        ];
    }

    public function save()
    {
        $this->validate([
            'entry_date'  => 'required|date',
            'description' => 'required|string|max:255',
            'currency_id' => 'required',
        ]);

        $data = [
            'entry_date'    => $this->entry_date,
            'description'   => $this->description,
            'narration'     => $this->narration,
            'entry_type'    => $this->entry_type,
            'currency_id'   => $this->currency_id,
            'exchange_rate' => $this->exchange_rate ?? 1,
        ];

        $response = $this->id
            ? $this->journalRepo->update($this->id, $data, $this->lines)
            : $this->journalRepo->create($data, $this->lines);

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
        $response = $this->journalRepo->post($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function viewEntry($id)
    {
        $this->selectedEntry = $this->journalRepo->get($id);
        $this->viewModal     = true;
    }

    public function openReverseModal($id)
    {
        $this->id            = $id;
        $this->reverseReason = '';
        $this->reverseModal  = true;
    }

    public function reverse()
    {
        $this->validate(['reverseReason' => 'required|string|max:255']);
        $response = $this->journalRepo->reverse($this->id, $this->reverseReason);
        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->reverseModal = false;
            $this->reset(['id', 'reverseReason']);
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $entry              = $this->journalRepo->get($id);
        $this->id           = $entry->id;
        $this->entry_date   = $entry->entry_date->format('Y-m-d');
        $this->description  = $entry->description;
        $this->narration    = $entry->narration;
        $this->entry_type   = $entry->entry_type;
        $this->currency_id  = $entry->currency_id;
        $this->exchange_rate = $entry->exchange_rate;
        $this->lines        = $entry->lines->map(fn($l) => [
            'account_id'     => $l->account_id,
            'cost_center_id' => $l->cost_center_id,
            'description'    => $l->description,
            'debit'          => $l->debit,
            'credit'         => $l->credit,
        ])->toArray();
        $this->modal = true;
    }

    public function delete($id)
    {
        $response = $this->journalRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['id', 'description', 'narration', 'currency_id', 'exchange_rate']);
        $this->entry_date = date('Y-m-d');
        $this->entry_type = 'MANUAL';
        $this->lines      = [];
        $this->addLine();
        $this->addLine();
    }

    public function headers(): array
    {
        return [
            ['key' => 'reference_number', 'label' => 'Reference'],
            ['key' => 'entry_date',       'label' => 'Date'],
            ['key' => 'description',      'label' => 'Description'],
            ['key' => 'entry_type',       'label' => 'Type'],
            ['key' => 'total_debit',      'label' => 'Debit'],
            ['key' => 'total_credit',     'label' => 'Credit'],
            ['key' => 'status',           'label' => 'Status'],
            ['key' => 'action',           'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.journal-entries', [
            'entries'     => $this->getEntries(),
            'headers'     => $this->headers(),
            'accounts'    => $this->coaRepo->getPostable(),
            'costCenters' => $this->ccRepo->getAll('active'),
            'currencies'  => $this->currencyRepo->getAll('active'),
            'periods'     => $this->periodRepo->getOpen(),
            'totals'      => $this->getTotals(),
        ]);
    }
}
