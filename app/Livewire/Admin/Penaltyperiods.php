<?php

namespace App\Livewire\Admin;

use App\Interfaces\ipenaltyperiodInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Penaltyperiods extends Component
{
    use Toast;

    public $name;
    public $start_date;
    public $end_date;
    public $status = 'active';
    public $id;
    public $modal = false;
    public $breadcrumbs = [];

    protected $repo;

    public function boot(ipenaltyperiodInterface $repo)
    {
        $this->repo = $repo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Penalty Periods'],
        ];
    }

    public function statusOptions(): array
    {
        return [
            ['id' => 'active',   'label' => 'Active'],
            ['id' => 'inactive', 'label' => 'Inactive'],
        ];
    }

    public function save()
    {
        $this->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|string',
            'end_date'   => 'required|string',
            'status'     => 'required|in:active,inactive',
        ]);

        $data = [
            'name'       => $this->name,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'status'     => $this->status,
        ];

        $response = $this->id
            ? $this->repo->update($this->id, $data)
            : $this->repo->create($data);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->modal = false;
            $this->reset(['id', 'name', 'start_date', 'end_date']);
            $this->status = 'active';
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $record = $this->repo->get($id);
        $this->id         = $record->id;
        $this->name       = $record->name;
        $this->start_date = $record->start_date;
        $this->end_date   = $record->end_date;
        $this->status     = $record->status;
        $this->modal      = true;
    }

    public function delete($id)
    {
        $response = $this->repo->delete($id);
        $response['status'] === 'success'
            ? $this->success($response['message'])
            : $this->error($response['message']);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name',       'label' => 'Name'],
            ['key' => 'start_date', 'label' => 'Start Date'],
            ['key' => 'end_date',   'label' => 'End Date'],
            ['key' => 'status',     'label' => 'Status'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.penaltyperiods', [
            'headers'       => $this->headers(),
            'records'       => $this->repo->getAll(),
            'statusoptions' => $this->statusOptions(),
        ]);
    }
}
