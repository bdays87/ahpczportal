<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iaccountingperiodInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class AccountingPeriods extends Component
{
    use Toast;

    public $breadcrumbs = [];
    public $modal       = false;
    public $id;
    public $name;
    public $year;
    public $month;
    public $start_date;
    public $end_date;
    public $status = 'OPEN';
    public $confirmCloseId;
    public $confirmLockId;

    protected $repo;

    public function boot(iaccountingperiodInterface $repo)
    {
        $this->repo = $repo;
    }

    public function mount()
    {
        $this->year  = date('Y');
        $this->month = (int) date('n');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Accounting Periods'],
        ];
    }

    public function getPeriods()
    {
        return $this->repo->getAll();
    }

    public function save()
    {
        $this->validate([
            'name'       => 'required|string|max:100',
            'year'       => 'required|integer|min:2000|max:2100',
            'month'      => 'required|integer|min:1|max:12',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $data = [
            'name'       => $this->name,
            'year'       => $this->year,
            'month'      => $this->month,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'status'     => 'OPEN',
        ];

        if ($this->id) {
            $response = $this->repo->update($this->id, $data);
        } else {
            $response = $this->repo->create($data);
        }

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->reset(['id', 'name', 'year', 'month', 'start_date', 'end_date']);
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $period          = $this->repo->get($id);
        $this->id        = $period->id;
        $this->name      = $period->name;
        $this->year      = $period->year;
        $this->month     = $period->month;
        $this->start_date = $period->start_date;
        $this->end_date  = $period->end_date;
        $this->modal     = true;
    }

    public function close($id)
    {
        $response = $this->repo->close($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function lock($id)
    {
        $response = $this->repo->lock($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function delete($id)
    {
        $response = $this->repo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name',       'label' => 'Period'],
            ['key' => 'year',       'label' => 'Year'],
            ['key' => 'month',      'label' => 'Month'],
            ['key' => 'start_date', 'label' => 'Start Date'],
            ['key' => 'end_date',   'label' => 'End Date'],
            ['key' => 'status',     'label' => 'Status'],
            ['key' => 'action',     'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.accounting-periods', [
            'periods' => $this->getPeriods(),
            'headers' => $this->headers(),
        ]);
    }
}
