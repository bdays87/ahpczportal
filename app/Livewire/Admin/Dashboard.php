<?php

namespace App\Livewire\Admin;

use App\Models\Customerapplication;
use App\Models\Customerregistration;
use App\Models\Invoice;
use Livewire\Component;

class Dashboard extends Component
{
    public $breadcrumbs = [];

    public function mount(): void
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home'],
        ];
    }

    // Application Statistics
    public function getApplicationStatsProperty()
    {
        $currentYear = date('Y');

        return [
            'total' => Customerapplication::where('year', $currentYear)->count(),
            'pending' => Customerapplication::where('year', $currentYear)->where('status', 'PENDING')->count(),
            'awaiting' => Customerapplication::where('year', $currentYear)->where('status', 'AWAITING')->count(),
            'approved' => Customerapplication::where('year', $currentYear)->where('status', 'APPROVED')->count(),
            'rejected' => Customerapplication::where('year', $currentYear)->where('status', 'REJECTED')->count(),
        ];
    }

    // Registration Statistics
    public function getRegistrationStatsProperty()
    {
        $currentYear = date('Y');

        return [
            'total' => Customerregistration::where('year', $currentYear)->count(),
            'pending' => Customerregistration::where('year', $currentYear)->where('status', 'PENDING')->count(),
            'awaiting' => Customerregistration::where('year', $currentYear)->where('status', 'AWAITING')->count(),
            'approved' => Customerregistration::where('year', $currentYear)->where('status', 'APPROVED')->count(),
            'rejected' => Customerregistration::where('year', $currentYear)->where('status', 'REJECTED')->count(),
        ];
    }

    // Invoice Statistics
    public function getInvoiceStatsProperty()
    {
        $currentYear = date('Y');

        return [
            'total' => Invoice::where('year', $currentYear)->count(),
            'total_amount' => Invoice::where('year', $currentYear)->sum('amount'),
            'paid' => Invoice::where('year', $currentYear)->where('status', 'PAID')->count(),
            'paid_amount' => Invoice::where('year', $currentYear)->where('status', 'PAID')->sum('amount'),
            'pending' => Invoice::where('year', $currentYear)->whereIn('status', ['PENDING', 'AWAITING'])->count(),
            'pending_amount' => Invoice::where('year', $currentYear)->whereIn('status', ['PENDING', 'AWAITING'])->sum('amount'),
        ];
    }

    // Recent Applications
    public function getRecentApplicationsProperty()
    {
        return Customerapplication::with([
            'customerprofession.customer',
            'customerprofession.profession',
            'applicationtype',
        ])
            ->whereHas('customerprofession.customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'applicationStats' => $this->applicationStats,
            'registrationStats' => $this->registrationStats,
            'invoiceStats' => $this->invoiceStats,
            'recentApplications' => $this->recentApplications,
        ]);
    }
}
