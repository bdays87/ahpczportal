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

    /** Services/tests offered; each: ['name','description','subtests'=>[['name'],..]] */
    public array $services = [];

    /** Accreditations; each: ['name','level'] */
    public array $accreditations = [];

    public $employmenttype = 'PERMANENT';

    public $assignRegistrationDate;

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
        $this->reset('customersearch', 'employees', 'services', 'accreditations');
        $this->employmenttype = 'PERMANENT';
        // Pre-fill the registration date from the imported value if it parses,
        // otherwise default to today. The admin can change it.
        try {
            $this->assignRegistrationDate = $import->registration_date
                ? \Carbon\Carbon::parse($import->registration_date)->format('Y-m-d')
                : now()->format('Y-m-d');
        } catch (\Throwable $e) {
            $this->assignRegistrationDate = now()->format('Y-m-d');
        }
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
        // First employee added is the practitioner-in-charge.
        $role = count($this->employees) === 0 ? 'IN_CHARGE' : 'EMPLOYEE';
        $this->employees[] = ['id' => $id, 'name' => $name, 'regnumber' => $regnumber, 'role' => $role];
        $this->customersearch = '';
    }

    public function removeemployee($id)
    {
        $this->employees = array_values(array_filter($this->employees, fn ($e) => $e['id'] != $id));
        // Keep the first employee flagged as in-charge.
        if (! empty($this->employees)) {
            foreach ($this->employees as $i => $e) {
                $this->employees[$i]['role'] = $i === 0 ? 'IN_CHARGE' : ($e['role'] === 'IN_CHARGE' ? 'EMPLOYEE' : $e['role']);
            }
        }
    }

    // Services / tests repeater
    public function addservice()
    {
        $this->services[] = ['name' => '', 'description' => '', 'subtests' => []];
    }

    public function removeservice($index)
    {
        unset($this->services[$index]);
        $this->services = array_values($this->services);
    }

    public function addsubtest($index)
    {
        $this->services[$index]['subtests'][] = ['name' => ''];
    }

    public function removesubtest($index, $subindex)
    {
        unset($this->services[$index]['subtests'][$subindex]);
        $this->services[$index]['subtests'] = array_values($this->services[$index]['subtests']);
    }

    // Accreditations repeater
    public function addaccreditation()
    {
        $this->accreditations[] = ['name' => '', 'level' => ''];
    }

    public function removeaccreditation($index)
    {
        unset($this->accreditations[$index]);
        $this->accreditations = array_values($this->accreditations);
    }

    public function assign()
    {
        $this->validate([
            'employees' => 'required|array|min:1',
            'assignRegistrationDate' => 'required|date',
        ], [
            'employees.required' => 'Please add at least one employee (the first is the practitioner-in-charge).',
            'employees.min' => 'Please add at least one employee (the first is the practitioner-in-charge).',
            'assignRegistrationDate.required' => 'Please enter the registration date.',
        ]);

        $response = $this->repo->assigninstitutionimport($this->assignid, [
            'employees' => array_map(fn ($e) => ['id' => $e['id'], 'role' => $e['role'] ?? 'EMPLOYEE'], $this->employees),
            'employmenttype' => $this->employmenttype,
            'registration_date' => $this->assignRegistrationDate,
            'services' => $this->services,
            'accreditations' => $this->accreditations,
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
