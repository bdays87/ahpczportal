<?php

namespace App\implementations;

use App\Interfaces\idashboardInterface;
use App\Models\Customerapplication;
use App\Models\Customerregistration;
use App\Models\Invoice;

class _dashboardRepository implements idashboardInterface
{
    protected $customerapplication;

    protected $customerregistration;

    protected $invoice;

    public function __construct(
        Customerapplication $customerapplication,
        Customerregistration $customerregistration,
        Invoice $invoice
    ) {
        $this->customerapplication = $customerapplication;
        $this->customerregistration = $customerregistration;
        $this->invoice = $invoice;
    }

    public function getApplicationStatistics($year = null)
    {
        $year = $year ?? date('Y');

        return [
            'total' => $this->customerapplication->where('year', $year)->count(),
            'pending' => $this->customerapplication->where('year', $year)->where('status', 'PENDING')->count(),
            'awaiting' => $this->customerapplication->where('year', $year)->where('status', 'AWAITING')->count(),
            'approved' => $this->customerapplication->where('year', $year)->where('status', 'APPROVED')->count(),
            'rejected' => $this->customerapplication->where('year', $year)->where('status', 'REJECTED')->count(),
        ];
    }

    public function getRegistrationStatistics($year = null)
    {
        $year = $year ?? date('Y');

        return [
            'total' => $this->customerregistration->where('year', $year)->count(),
            'pending' => $this->customerregistration->where('year', $year)->where('status', 'PENDING')->count(),
            'awaiting' => $this->customerregistration->where('year', $year)->where('status', 'AWAITING')->count(),
            'approved' => $this->customerregistration->where('year', $year)->where('status', 'APPROVED')->count(),
            'rejected' => $this->customerregistration->where('year', $year)->where('status', 'REJECTED')->count(),
        ];
    }

    public function getInvoiceStatistics($year = null)
    {
        $year = $year ?? date('Y');

        return [
            'total' => $this->invoice->where('year', $year)->count(),
            'total_amount' => $this->invoice->where('year', $year)->sum('amount'),
            'paid' => $this->invoice->where('year', $year)->where('status', 'PAID')->count(),
            'paid_amount' => $this->invoice->where('year', $year)->where('status', 'PAID')->sum('amount'),
            'pending' => $this->invoice->where('year', $year)->whereIn('status', ['PENDING', 'AWAITING'])->count(),
            'pending_amount' => $this->invoice->where('year', $year)->whereIn('status', ['PENDING', 'AWAITING'])->sum('amount'),
            'settled' => $this->invoice->where('year', $year)->where('status', 'SETTLED')->count(),
            'settled_amount' => $this->invoice->where('year', $year)->where('status', 'SETTLED')->sum('amount'),
        ];
    }

    public function getRecentApplications($limit = 10)
    {
        return $this->customerapplication
            ->with([
                'customerprofession.customer',
                'customerprofession.profession',
                'customerprofession.registertype',
                'applicationtype',
            ])
            ->whereHas('customerprofession.customer')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getApplicationsByProfession($year = null)
    {
        $year = $year ?? date('Y');

        return $this->customerapplication
            ->join('customerprofessions', 'customerapplications.customerprofession_id', '=', 'customerprofessions.id')
            ->join('professions', 'customerprofessions.profession_id', '=', 'professions.id')
            ->where('customerapplications.year', $year)
            ->selectRaw('professions.name as profession_name, count(*) as count')
            ->groupBy('professions.id', 'professions.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->profession_name,
                    'count' => $item->count,
                ];
            });
    }

    public function getApplicationsByRegistertype($year = null)
    {
        $year = $year ?? date('Y');

        return $this->customerapplication
            ->join('customerprofessions', 'customerapplications.customerprofession_id', '=', 'customerprofessions.id')
            ->join('registertypes', 'customerprofessions.registertype_id', '=', 'registertypes.id')
            ->where('customerapplications.year', $year)
            ->selectRaw('registertypes.name as registertype_name, count(*) as count')
            ->groupBy('registertypes.id', 'registertypes.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->registertype_name,
                    'count' => $item->count,
                ];
            });
    }

    public function getApplicationsByCustomertype($year = null)
    {
        $year = $year ?? date('Y');

        return $this->customerapplication
            ->join('customerprofessions', 'customerapplications.customerprofession_id', '=', 'customerprofessions.id')
            ->join('customertypes', 'customerprofessions.customertype_id', '=', 'customertypes.id')
            ->where('customerapplications.year', $year)
            ->selectRaw('customertypes.name as customertype_name, count(*) as count')
            ->groupBy('customertypes.id', 'customertypes.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->customertype_name,
                    'count' => $item->count,
                ];
            });
    }
}
