<?php

namespace App\Livewire\Admin\Reports;

use App\Interfaces\iapplicationsessionInterface;
use App\Interfaces\irevenueInterface;
use App\Models\City;
use App\Models\Province;
use Livewire\Component;
use Mary\Traits\Toast;

class Revenue extends Component
{
    use Toast;

    // Filter properties
    public $date_from;

    public $date_to;

    public $province_id;

    public $city_id;

    public $year;

    public $status;

    public $source;

    public $search;

    // Repositories
    protected $revenueRepo;

    protected $sessionRepo;

    public $breadcrumbs = [];

    public function boot(
        irevenueInterface $revenueRepo,
        iapplicationsessionInterface $sessionRepo
    ): void {
        $this->revenueRepo = $revenueRepo;
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
                'label' => 'Revenue Report',
            ],
        ];

        // Set default filters
        $this->year = date('Y');
    }

    public function getRevenueDataProperty()
    {
        return $this->revenueRepo->reportData([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'year' => $this->year,
            'status' => $this->status,
            'source' => $this->source,
            'search' => $this->search,
        ]);
    }

    public function getSummaryStatsProperty()
    {
        return $this->revenueRepo->getSummaryStats([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'year' => $this->year,
            'status' => $this->status,
            'source' => $this->source,
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

    public function getApplicationSessionsProperty()
    {
        return $this->sessionRepo->getAll();
    }

    public function getStatusOptionsProperty()
    {
        return [
            ['id' => 'PAID', 'name' => 'Paid'],
            ['id' => 'PENDING', 'name' => 'Pending'],
            ['id' => 'AWAITING', 'name' => 'Awaiting'],
            ['id' => 'SETTLED', 'name' => 'Settled'],
        ];
    }

    public function getSourceOptionsProperty()
    {
        return [
            ['id' => 'REGISTRATION', 'name' => 'Registration'],
            ['id' => 'APPLICATION', 'name' => 'Application'],
            ['id' => 'RENEWAL', 'name' => 'Renewal'],
            ['id' => 'CASH', 'name' => 'Cash'],
            ['id' => 'EFT', 'name' => 'EFT'],
            ['id' => 'MOBILE', 'name' => 'Mobile Money'],
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
        $this->year = date('Y');
        $this->status = null;
        $this->source = null;
        $this->search = null;

        $this->success('Filters cleared successfully');
    }

    public function exportToCsv()
    {
        $revenueData = $this->revenueData;

        if ($revenueData->isEmpty()) {
            $this->warning('No data to export');

            return null;
        }

        $filename = 'revenue_report_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, [
            'Transaction Date',
            'Type',
            'Reference',
            'Customer Name',
            'Customer Email',
            'Province',
            'City',
            'Description',
            'Source',
            'Amount',
            'Currency',
            'Status',
            'Year',
        ]);

        // Add data rows
        foreach ($revenueData as $transaction) {
            fputcsv($file, [
                $transaction['transaction_date']->format('Y-m-d H:i:s'),
                $transaction['type'],
                $transaction['reference'],
                ($transaction['customer']->name ?? '').' '.($transaction['customer']->surname ?? ''),
                $transaction['customer']->email ?? 'N/A',
                $transaction['customer']->province?->name ?? 'N/A',
                $transaction['customer']->city?->name ?? 'N/A',
                $transaction['description'],
                $transaction['source'],
                number_format($transaction['amount'], 2),
                $transaction['currency']->code ?? 'N/A',
                $transaction['status'],
                $transaction['year'],
            ]);
        }

        fclose($file);

        $this->success('Report exported successfully');

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.reports.revenue', [
            'revenueData' => $this->revenueData,
            'summaryStats' => $this->summaryStats,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'applicationSessions' => $this->applicationSessions,
            'statusOptions' => $this->statusOptions,
            'sourceOptions' => $this->sourceOptions,
        ]);
    }
}
