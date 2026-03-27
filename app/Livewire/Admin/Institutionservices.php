<?php

namespace App\Livewire\Admin;

use App\Interfaces\iinstitutionserviceInterface;
use App\Interfaces\institutionInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Institutionservices extends Component
{
    use Toast;

    public $institution_id;
    public $name;
    public $description;
    public $status = 'active';
    public $id;
    public $modal = false;
    public $breadcrumbs = [];

    protected $repo;
    protected $institutionrepo;

    public function boot(iinstitutionserviceInterface $repo, institutionInterface $institutionrepo)
    {
        $this->repo            = $repo;
        $this->institutionrepo = $institutionrepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Institution Services'],
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
            'institution_id' => 'required|exists:institutions,id',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'status'         => 'required|in:active,inactive',
        ]);

        $data = [
            'institution_id' => $this->institution_id,
            'name'           => $this->name,
            'description'    => $this->description,
            'status'         => $this->status,
        ];

        $response = $this->id
            ? $this->repo->update($this->id, $data)
            : $this->repo->create($data);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->modal = false;
            $this->reset(['id', 'institution_id', 'name', 'description']);
            $this->status = 'active';
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $record                = $this->repo->get($id);
        $this->id              = $record->id;
        $this->institution_id  = $record->institution_id;
        $this->name            = $record->name;
        $this->description     = $record->description;
        $this->status          = $record->status;
        $this->modal           = true;
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
            ['key' => 'institution.name', 'label' => 'Institution'],
            ['key' => 'name',             'label' => 'Service Name'],
            ['key' => 'description',      'label' => 'Description'],
            ['key' => 'status',           'label' => 'Status'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.institutionservices', [
            'headers'       => $this->headers(),
            'records'       => $this->repo->getAll(),
            'institutions'  => $this->institutionrepo->getAllOptions(),
            'statusoptions' => $this->statusOptions(),
        ]);
    }
}
