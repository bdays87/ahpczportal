<?php

namespace App\Livewire\Admin;

use App\Interfaces\irestorationfeeInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Restorationfees extends Component
{
    use Toast;

    public $name;
    public $amount;
    public $status = 'active';
    public $id;
    public $modal = false;
    public $breadcrumbs = [];

    protected $restorationfeerepo;

    public function boot(irestorationfeeInterface $restorationfeerepo)
    {
        $this->restorationfeerepo = $restorationfeerepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Restoration Fees'],
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
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $response = $this->id
            ? $this->restorationfeerepo->update($this->id, ['name' => $this->name, 'amount' => $this->amount, 'status' => $this->status])
            : $this->restorationfeerepo->create(['name' => $this->name, 'amount' => $this->amount, 'status' => $this->status]);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->modal = false;
            $this->reset(['id', 'name', 'amount', 'status']);
            $this->status = 'active';
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $fee = $this->restorationfeerepo->get($id);
        $this->id     = $fee->id;
        $this->name   = $fee->name;
        $this->amount = $fee->amount;
        $this->status = $fee->status;
        $this->modal  = true;
    }

    public function delete($id)
    {
        $response = $this->restorationfeerepo->delete($id);
        $response['status'] === 'success'
            ? $this->success($response['message'])
            : $this->error($response['message']);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name',   'label' => 'Name'],
            ['key' => 'amount', 'label' => 'Amount'],
            ['key' => 'status', 'label' => 'Status'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.restorationfees', [
            'headers'       => $this->headers(),
            'fees'          => $this->restorationfeerepo->getAll(),
            'statusoptions' => $this->statusOptions(),
        ]);
    }
}
