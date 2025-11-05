<?php

namespace App\Livewire\Admin\Reports;

use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\icustomerregistrationInterface;
use App\Models\City;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Livewire\Component;
use Mary\Traits\Toast;

class Registrations extends Component
{
    use Toast;

    // Filter properties
    public $date_from;

    public $date_to;

    public $province_id;

    public $city_id;

    public $gender;

    public $registertype_id;

    public $year;

    public $profession_id;

    public $status;

    public $search;

    // Repositories
    protected $registrationRepo;

    protected $sessionRepo;

    public $breadcrumbs = [];

    public function boot(
        icustomerregistrationInterface $registrationRepo,
        iapplicationsessionInterface $sessionRepo
    ): void {
        $this->registrationRepo = $registrationRepo;
        $this->sessionRepo = $sessionRepo;
    }

    public function mount(): void
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Reports',
            ],
            [
                'label' => 'Registrations Report',
            ],
        ];

        // Set default filters
        $this->year = date('Y');
    }

    public function getRegistrationsProperty()
    {
        return $this->registrationRepo->reportData([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'registertype_id' => $this->registertype_id,
            'year' => $this->year,
            'profession_id' => $this->profession_id,
            'status' => $this->status,
            'search' => $this->search,
        ]);
    }

    public function getProvincesProperty()
    {
        return Province::orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if ($this->province_id) {
            return City::where('province_id', $this->province_id)->orderBy('name')->get();
        }

        return City::orderBy('name')->get();
    }

    public function getProfessionsProperty()
    {
        return Profession::orderBy('name')->get();
    }

    public function getRegistertypesProperty()
    {
        return Registertype::orderBy('name')->get();
    }

    public function getApplicationSessionsProperty()
    {
        return $this->sessionRepo->getAll();
    }

    public function getGenderOptionsProperty()
    {
        return [
            ['id' => 'MALE', 'name' => 'Male'],
            ['id' => 'FEMALE', 'name' => 'Female'],
            ['id' => 'OTHER', 'name' => 'Other'],
        ];
    }

    public function getStatusOptionsProperty()
    {
        return [
            ['id' => 'APPROVED', 'name' => 'Approved'],
            ['id' => 'PENDING', 'name' => 'Pending'],
            ['id' => 'AWAITING', 'name' => 'Awaiting'],
            ['id' => 'REJECTED', 'name' => 'Rejected'],
        ];
    }

    public function updatedProvinceId(): void
    {
        $this->city_id = null;
    }

    public function clearFilters(): void
    {
        $this->date_from = null;
        $this->date_to = null;
        $this->province_id = null;
        $this->city_id = null;
        $this->gender = null;
        $this->registertype_id = null;
        $this->year = date('Y');
        $this->profession_id = null;
        $this->status = null;
        $this->search = null;

        $this->success('Filters cleared successfully');
    }

    public function exportToCsv()
    {
        $registrations = $this->registrations;

        if ($registrations->isEmpty()) {
            $this->warning('No data to export');

            return null;
        }

        $filename = 'registrations_report_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, [
            'Registration Date',
            'Year',
            'Practitioner Name',
            'Email',
            'Gender',
            'Province',
            'City',
            'Profession',
            'Register Type',
            'Status',
            'Certificate Number',
            'Certificate Expiry',
        ]);

        // Add data rows
        foreach ($registrations as $registration) {
            // Skip registrations with null relationships
            if (! $registration->customerprofession || ! $registration->customerprofession->customer) {
                continue;
            }

            fputcsv($file, [
                $registration->created_at->format('Y-m-d'),
                $registration->year,
                $registration->customerprofession->customer->name.' '.$registration->customerprofession->customer->surname,
                $registration->customerprofession->customer->email ?? 'N/A',
                $registration->customerprofession->customer->gender ?? 'N/A',
                $registration->customerprofession->customer->province?->name ?? 'N/A',
                $registration->customerprofession->customer->city?->name ?? 'N/A',
                $registration->customerprofession->profession->name ?? 'N/A',
                $registration->customerprofession->registertype->name ?? 'N/A',
                $registration->status,
                $registration->certificatenumber ?? 'N/A',
                $registration->certificateexpirydate?->format('Y-m-d') ?? 'N/A',
            ]);
        }

        fclose($file);

        $this->success('Report exported successfully');

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.reports.registrations', [
            'registrations' => $this->registrations,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'professions' => $this->professions,
            'registertypes' => $this->registertypes,
            'applicationSessions' => $this->applicationSessions,
            'genderOptions' => $this->genderOptions,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
