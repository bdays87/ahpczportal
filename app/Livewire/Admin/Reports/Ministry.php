<?php

namespace App\Livewire\Admin\Reports;

use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\icustomerapplicationInterface;
use App\Models\City;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Livewire\Component;
use Mary\Traits\Toast;

class Ministry extends Component
{
    use Toast;

    // Filter properties
    public $date_from;

    public $date_to;

    public $province_id;

    public $city_id;

    public $gender;

    public $age_from;

    public $age_to;

    public $profession_id;

    public $registertype_id;

    public $place_of_birth;

    public $compliance;

    public $year;

    public $search;

    // Repositories
    protected $applicationRepo;

    protected $sessionRepo;

    public $breadcrumbs = [];

    public function boot(
        icustomerapplicationInterface $applicationRepo,
        iapplicationsessionInterface $sessionRepo
    ): void {
        $this->applicationRepo = $applicationRepo;
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
                'label' => 'Ministry Report',
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
            'search' => $this->search,
        ]);

        // Filter by age range if specified
        if ($this->age_from || $this->age_to) {
            $applications = $applications->filter(function ($app) {
                if (! $app->customerprofession || ! $app->customerprofession->customer) {
                    return false;
                }

                $age = $app->customerprofession->customer->getage();

                if ($this->age_from && $age < $this->age_from) {
                    return false;
                }

                if ($this->age_to && $age > $this->age_to) {
                    return false;
                }

                return true;
            });
        }

        // Filter by place of birth if specified
        if ($this->place_of_birth) {
            $applications = $applications->filter(function ($app) {
                if (! $app->customerprofession || ! $app->customerprofession->customer) {
                    return false;
                }

                return stripos($app->customerprofession->customer->place_of_birth, $this->place_of_birth) !== false;
            });
        }

        // Filter by compliance status if specified (only for approved applications)
        if ($this->compliance === 'Valid') {
            $applications = $applications->filter(fn ($app) => $app->status === 'APPROVED' && $app->isValid());
        } elseif ($this->compliance === 'Expired') {
            $applications = $applications->filter(fn ($app) => $app->status === 'APPROVED' && $app->isExpired());
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

    public function getGenderOptionsProperty()
    {
        return [
            ['id' => 'MALE', 'name' => 'Male'],
            ['id' => 'FEMALE', 'name' => 'Female'],
            ['id' => 'OTHER', 'name' => 'Other'],
        ];
    }

    public function getComplianceOptionsProperty()
    {
        return [
            ['id' => 'Valid', 'name' => 'Valid'],
            ['id' => 'Expired', 'name' => 'Expired'],
        ];
    }

    public function getDemographicsProperty()
    {
        $applications = $this->applications;

        $totalApproved = $applications->where('status', 'APPROVED')->count();

        return [
            'total' => $applications->count(),
            'approved' => $totalApproved,
            'male' => $applications->filter(fn ($app) => $app->customerprofession?->customer?->gender === 'MALE')->count(),
            'female' => $applications->filter(fn ($app) => $app->customerprofession?->customer?->gender === 'FEMALE')->count(),
            'valid' => $applications->filter(fn ($app) => $app->status === 'APPROVED' && $app->isValid())->count(),
            'expired' => $applications->filter(fn ($app) => $app->status === 'APPROVED' && $app->isExpired())->count(),
            'avg_age' => $applications->filter(fn ($app) => $app->customerprofession?->customer)
                ->map(fn ($app) => (int) $app->customerprofession->customer->getage())
                ->avg() ?? 0,
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
        $this->age_from = null;
        $this->age_to = null;
        $this->profession_id = null;
        $this->registertype_id = null;
        $this->place_of_birth = null;
        $this->compliance = null;
        $this->year = date('Y');
        $this->search = null;

        $this->success('Filters cleared successfully');
    }

    public function exportToCsv()
    {
        $applications = $this->applications;

        if ($applications->isEmpty()) {
            $this->warning('No data to export');

            return null;
        }

        $filename = 'ministry_report_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, [
            'Application Date',
            'Year',
            'Practitioner Name',
            'Email',
            'Gender',
            'Age',
            'Date of Birth',
            'Place of Birth',
            'Province',
            'City',
            'Profession',
            'Register Type',
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

            $customer = $application->customerprofession->customer;

            fputcsv($file, [
                $application->created_at->format('Y-m-d'),
                $application->year,
                $customer->name.' '.$customer->surname,
                $customer->email ?? 'N/A',
                $customer->gender ?? 'N/A',
                (int) $customer->getage(),
                $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('Y-m-d') : 'N/A',
                $customer->place_of_birth ?? 'N/A',
                $customer->province?->name ?? 'N/A',
                $customer->city?->name ?? 'N/A',
                $application->customerprofession->profession->name ?? 'N/A',
                $application->customerprofession->registertype->name ?? 'N/A',
                $application->status,
                $application->certificate_number ?? 'N/A',
                $application->certificate_expiry_date?->format('Y-m-d') ?? 'N/A',
                $application->status === 'APPROVED' ? $application->certificate_status : 'N/A',
            ]);
        }

        fclose($file);

        $this->success('Report exported successfully');

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.reports.ministry', [
            'applications' => $this->applications,
            'demographics' => $this->demographics,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'professions' => $this->professions,
            'registertypes' => $this->registertypes,
            'applicationSessions' => $this->applicationSessions,
            'genderOptions' => $this->genderOptions,
            'complianceOptions' => $this->complianceOptions,
        ]);
    }
}
