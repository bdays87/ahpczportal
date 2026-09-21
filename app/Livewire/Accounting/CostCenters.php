<?php

namespace App\Livewire\Accounting;

use App\Interfaces\icostcenterInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class CostCenters extends Component
{
    use Toast;

    public $breadcrumbs = [];
    public $modal       = false;
    public $id;
    public $code;
    public $name;
    public $description;
    public $parent_id;
    public $status = 'active';

    protected $repo;

    public function boot(icostcenterInterface $repo)
    {
        $this->repo = $repo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Cost Centers'],
        ];
    }

    public function getCostCenters()
    {
        return $this->repo->getAll();
    }

    public function getParents()
    {
        return $this->repo->getAll('active');
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:150',
        ]);

        $data = [
            'code'        => $this->code,
            'name'        => $this->name,
            'description' => $this->description,
            'parent_id'   => $this->parent_id ?: null,
            'status'      => $this->status,
        ];

        $response = $this->id
            ? $this->repo->update($this->id, $data)
            : $this->repo->create($data);

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
        $cc                = $this->repo->get($id);
        $this->id          = $cc->id;
        $this->code        = $cc->code;
        $this->name        = $cc->name;
        $this->description = $cc->description;
        $this->parent_id   = $cc->parent_id;
        $this->status      = $cc->status;
        $this->modal       = true;
    }

    public function delete($id)
    {
        $response = $this->repo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['id', 'code', 'name', 'description', 'parent_id']);
        $this->status = 'active';
    }

    public function headers(): array
    {
        return [
            ['key' => 'code',        'label' => 'Code'],
            ['key' => 'name',        'label' => 'Name'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'parent.name', 'label' => 'Parent'],
            ['key' => 'status',      'label' => 'Status'],
            ['key' => 'action',      'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.cost-centers', [
            'costCenters' => $this->getCostCenters(),
            'headers'     => $this->headers(),
            'parents'     => $this->getParents(),
        ]);
    }
}
