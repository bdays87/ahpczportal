<?php

namespace App\Livewire\Admin\Reports;

use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\iapplicationtypeInterface;
use App\Interfaces\icustomerapplicationInterface;
use App\Models\City;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Livewire\Component;
use Mary\Traits\Toast;

class Applications extends Component
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

    public $applicationtype_id;

    public $search;

    public $compliance;

    // Repositories
    protected $applicationRepo;

    protected $sessionRepo;

    protected $applicationTypeRepo;

    public $breadcrumbs = [];

    public function boot(
        icustomerapplicationInterface $applicationRepo,
        iapplicationsessionInterface $sessionRepo,
        iapplicationtypeInterface $applicationTypeRepo
    ): void {
        $this->applicationRepo = $applicationRepo;
        $this->sessionRepo = $sessionRepo;
        $this->applicationTypeRepo = $applicationTypeRepo;
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
                'label' => 'Applications Report',
            ],
        ];

        // Set default filters
        $this->year = date('Y');
    }

    public function getApplicationsProperty()
    {
        $applications = $this->applicationRepo->reportData([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'registertype_id' => $this->registertype_id,
            'year' => $this->year,
            'profession_id' => $this->profession_id,
            'status' => $this->status,
            'applicationtype_id' => $this->applicationtype_id,
            'search' => $this->search,
        ]);

        // Filter by compliance status if specified (only for approved applications)
        if ($this->compliance === 'Valid') {
            return $applications->filter(fn ($app) => $app->status === 'APPROVED' && $app->isValid());
        } elseif ($this->compliance === 'Expired') {
            return $applications->filter(fn ($app) => $app->status === 'APPROVED' && $app->isExpired());
        }

        return $applications;
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

    public function getApplicationTypesProperty()
    {
        return $this->applicationTypeRepo->getAll();
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
            ['id' => 'PENDING', 'name' => 'Pending'],
            ['id' => 'AWAITING', 'name' => 'Awaiting'],
            ['id' => 'APPROVED', 'name' => 'Approved'],
            ['id' => 'REJECTED', 'name' => 'Rejected'],
        ];
    }

    public function getComplianceOptionsProperty()
    {
        return [
            ['id' => 'Valid', 'name' => 'Valid'],
            ['id' => 'Expired', 'name' => 'Expired'],
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
        $this->applicationtype_id = null;
        $this->search = null;
        $this->compliance = null;

        $this->success('Filters cleared successfully');
    }

    public function exportToCsv()
    {
        $applications = $this->applications;

        if ($applications->isEmpty()) {
            $this->warning('No data to export');

            return null;
        }

        $filename = 'applications_report_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, [
            'Application Date',
            'Year',
            'Practitioner Name',
            'Email',
            'Gender',
            'Province',
            'City',
            'Profession',
            'Register Type',
            'Application Type',
            'Status',
            'Certificate Number',
            'Certificate Expiry',
            'Compliance Status',
        ]);

        // Add data rows
        foreach ($applications as $application) {
            // Skip applications with null relationships
            if (! $application->customerprofession || ! $application->customerprofession->customer) {
                continue;
            }

            fputcsv($file, [
                $application->created_at->format('Y-m-d'),
                $application->year,
                $application->customerprofession->customer->name.' '.$application->customerprofession->customer->surname,
                $application->customerprofession->customer->email ?? 'N/A',
                $application->customerprofession->customer->gender ?? 'N/A',
                $application->customerprofession->customer->province?->name ?? 'N/A',
                $application->customerprofession->customer->city?->name ?? 'N/A',
                $application->customerprofession->profession->name ?? 'N/A',
                $application->customerprofession->registertype->name ?? 'N/A',
                $application->applicationtype->name ?? 'N/A',
                $application->status,
                $application->certificate_number ?? 'N/A',
                $application->certificate_expiry_date?->format('Y-m-d') ?? 'N/A',
                $application->certificate_status ?? 'N/A',
            ]);
        }

        fclose($file);

        $this->success('Report exported successfully');

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.reports.applications', [
            'applications' => $this->applications,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'professions' => $this->professions,
            'registertypes' => $this->registertypes,
            'applicationSessions' => $this->applicationSessions,
            'applicationTypes' => $this->applicationTypes,
            'genderOptions' => $this->genderOptions,
            'statusOptions' => $this->statusOptions,
            'complianceOptions' => $this->complianceOptions,
        ]);
    }
}
