<?php

namespace App\Livewire\Admin;

use App\Interfaces\iinstitutionserviceInterface;
use App\Interfaces\institutionInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Institutions extends Component
{
    use Toast, WithPagination;

    public $search;
    public $name;
    public $accredited;
    public $id;
    public $modal = false;

    public $servicesmodal = false;
    public $selectedinstitutionid = null;
    public $servicename;
    public $servicedescription;
    public $servicestatus = 'active';

    public $breadcrumbs = [];

    protected $repo;
    protected $servicerepo;

    public function boot(institutionInterface $repo, iinstitutionserviceInterface $servicerepo)
    {
        $this->repo        = $repo;
        $this->servicerepo = $servicerepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Institutions'],
        ];
    }

    public function getinstitutions()
    {
        return $this->repo->getAll($this->search);
    }

    public function save()
    {
        $this->validate(['name' => 'required', 'accredited' => 'required']);
        $this->id ? $this->update() : $this->create();
    }

    public function create()
    {
        $response = $this->repo->create(['name' => $this->name, 'accredited' => $this->accredited]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->modal = false;
            $this->reset(['id', 'name', 'accredited']);
        } else {
            $this->error($response['message']);
        }
    }

    public function update()
    {
        $response = $this->repo->update($this->id, ['name' => $this->name, 'accredited' => $this->accredited]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->modal = false;
            $this->reset(['id', 'name', 'accredited']);
        } else {
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->repo->delete($id);
        $response['status'] == 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function edit($id)
    {
        $institution = $this->repo->get($id);
        if (!$institution) { $this->error('Institution not found.'); return; }
        $this->id         = $institution->id;
        $this->name       = $institution->name;
        $this->accredited = $institution->accredited;
        $this->modal      = true;
    }

    public function viewservices($id)
    {
        $this->selectedinstitutionid = $id;
        $this->servicesmodal         = true;
        $this->reset(['servicename', 'servicedescription']);
        $this->servicestatus = 'active';
    }

    public function addservice()
    {
        $this->validate(['servicename' => 'required|string|max:255', 'servicestatus' => 'required']);
        $response = $this->servicerepo->create([
            'institution_id' => $this->selectedinstitutionid,
            'name'           => $this->servicename,
            'description'    => $this->servicedescription,
            'status'         => $this->servicestatus,
        ]);
        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->reset(['servicename', 'servicedescription']);
            $this->servicestatus = 'active';
        } else {
            $this->error($response['message']);
        }
    }

    public function deleteservice($id)
    {
        $response = $this->servicerepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name',       'label' => 'Name'],
            ['key' => 'accredited', 'label' => 'Accredited'],
        ];
    }

    public function render()
    {
        $selectedinstitution         = $this->selectedinstitutionid ? $this->repo->get($this->selectedinstitutionid) : null;
        $selectedinstitutionservices = $this->selectedinstitutionid ? $this->servicerepo->getByInstitution($this->selectedinstitutionid) : collect();

        return view('livewire.admin.institutions', [
            'institutions'                => $this->getinstitutions(),
            'headers'                     => $this->headers(),
            'selectedinstitution'         => $selectedinstitution,
            'selectedinstitutionservices' => $selectedinstitutionservices,
        ]);
    }
}