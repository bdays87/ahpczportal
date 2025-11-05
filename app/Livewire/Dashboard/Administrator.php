<?php

namespace App\Livewire\Dashboard;

use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\idashboardInterface;
use Livewire\Component;

class Administrator extends Component
{
    protected $dashboardRepo;

    protected $sessionRepo;

    public $breadcrumbs = [];

    public $selectedYear;

    public function boot(
        idashboardInterface $dashboardRepo,
        iapplicationsessionInterface $sessionRepo
    ): void {
        $this->dashboardRepo = $dashboardRepo;
        $this->sessionRepo = $sessionRepo;
    }

    public function mount(): void
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home'],
        ];
        $this->selectedYear = date('Y');
    }

    public function getApplicationStatsProperty()
    {
        return $this->dashboardRepo->getApplicationStatistics($this->selectedYear);
    }

    public function getRegistrationStatsProperty()
    {
        return $this->dashboardRepo->getRegistrationStatistics($this->selectedYear);
    }

    public function getInvoiceStatsProperty()
    {
        return $this->dashboardRepo->getInvoiceStatistics($this->selectedYear);
    }

    public function getRecentApplicationsProperty()
    {
        return $this->dashboardRepo->getRecentApplications(10);
    }

    public function getApplicationSessionsProperty()
    {
        return $this->sessionRepo->getAll();
    }

    public function getApplicationsByProfessionProperty()
    {
        return $this->dashboardRepo->getApplicationsByProfession($this->selectedYear);
    }

    public function getApplicationsByRegistertypeProperty()
    {
        return $this->dashboardRepo->getApplicationsByRegistertype($this->selectedYear);
    }

    public function getApplicationsByCustomertypeProperty()
    {
        return $this->dashboardRepo->getApplicationsByCustomertype($this->selectedYear);
    }

    public function render()
    {
        return view('livewire.dashboard.administrator', [
            'applicationStats' => $this->applicationStats,
            'registrationStats' => $this->registrationStats,
            'invoiceStats' => $this->invoiceStats,
            'recentApplications' => $this->recentApplications,
            'applicationSessions' => $this->applicationSessions,
            'applicationsByProfession' => $this->applicationsByProfession,
            'applicationsByRegistertype' => $this->applicationsByRegistertype,
            'applicationsByCustomertype' => $this->applicationsByCustomertype,
        ]);
    }
}
