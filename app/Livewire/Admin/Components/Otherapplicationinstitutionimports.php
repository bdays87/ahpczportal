<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icustomerInterface;
use App\Interfaces\idatamanagementInterface;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Otherapplicationinstitutionimports extends Component
{
    use Toast, WithFileUploads, WithPagination;

    public $file;

    public $search;

    // Assign modal
    public bool $assignmodal = false;

    public $assignid;

    public $assigninstitutionname;

    public $customersearch;

    /** Ordered list of selected employees; the first is the practitioner-in-charge. */
    public array $employees = [];

    public $employmenttype = 'PERMANENT';

    protected $repo;

    protected $customerrepo;

    public function boot(idatamanagementInterface $repo, icustomerInterface $customerrepo)
    {
        $this->repo = $repo;
        $this->customerrepo = $customerrepo;
    }

    public function saveimport()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,txt',
        ], [
            'file.mimes' => 'Please upload the institutions CSV file.',
        ]);

        $path = $this->file->store('institutionimports', 'local');
        $response = $this->repo->importinstitutions($path);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->reset('file');
    }

    public function getimports()
    {
        return $this->repo->getallinstitutionimports($this->search);
    }

    public function getcustomers()
    {
        if (! $this->customersearch || strlen($this->customersearch) < 2) {
            return collect();
        }

        return $this->customerrepo->getallsearch($this->customersearch);
    }

    public function openassign($id)
    {
        $import = $this->repo->getinstitutionimport($id);
        if (! $import) {
            $this->error('Institution not found');

            return;
        }
        $this->assignid = $id;
        $this->assigninstitutionname = $import->tradename;
        $this->reset('customersearch', 'employees');
        $this->employmenttype = 'PERMANENT';
        $this->assignmodal = true;
    }

    public function addemployee($id, $name, $regnumber = null)
    {
        // Avoid duplicates.
        foreach ($this->employees as $employee) {
            if ($employee['id'] == $id) {
                $this->customersearch = '';

                return;
            }
        }
        $this->employees[] = ['id' => $id, 'name' => $name, 'regnumber' => $regnumber];
        $this->customersearch = '';
    }

    public function removeemployee($id)
    {
        $this->employees = array_values(array_filter($this->employees, fn ($e) => $e['id'] != $id));
    }

    public function assign()
    {
        $this->validate([
            'employees' => 'required|array|min:1',
        ], [
            'employees.required' => 'Please add at least one employee (the first is the practitioner-in-charge).',
            'employees.min' => 'Please add at least one employee (the first is the practitioner-in-charge).',
        ]);

        $response = $this->repo->assigninstitutionimport($this->assignid, [
            'employees' => array_column($this->employees, 'id'),
            'employmenttype' => $this->employmenttype,
        ]);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->assignmodal = false;
            $this->reset('assignid', 'customersearch', 'employees');
        } else {
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->repo->deleteinstitutionimport($id);
        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'tradename', 'label' => 'Institution'],
            ['key' => 'institution_type', 'label' => 'Type'],
            ['key' => 'registration_no', 'label' => 'Reg No'],
            ['key' => 'city', 'label' => 'City'],
            ['key' => 'province', 'label' => 'Province'],
            ['key' => 'incharge', 'label' => 'Practitioner-in-charge'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'action', 'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.admin.components.otherapplicationinstitutionimports', [
            'imports' => $this->getimports(),
            'headers' => $this->headers(),
            'customers' => $this->getcustomers(),
        ]);
    }
}
