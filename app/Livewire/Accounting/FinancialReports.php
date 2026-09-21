<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iaccountingperiodInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\ifinancialreportInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class FinancialReports extends Component
{
    use Toast;

    public $breadcrumbs  = [];
    public $activeTab    = 'trial_balance';
    public $periodId;
    public $dateFrom;
    public $dateTo;
    public $asAt;
    public $accountId;

    // Report data
    public $trialBalance  = [];
    public $profitLoss    = [];
    public $balanceSheet  = [];
    public $cashFlow      = [];
    public $generalLedger = [];

    protected $reportRepo;
    protected $periodRepo;
    protected $coaRepo;

    public function boot(
        ifinancialreportInterface $reportRepo,
        iaccountingperiodInterface $periodRepo,
        ichartofaccountsInterface $coaRepo
    ) {
        $this->reportRepo = $reportRepo;
        $this->periodRepo = $periodRepo;
        $this->coaRepo    = $coaRepo;
    }

    public function mount()
    {
        $this->dateFrom = date('Y-01-01');
        $this->dateTo   = date('Y-m-d');
        $this->asAt     = date('Y-m-d');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Financial Reports'],
        ];
    }

    public function runTrialBalance()
    {
        $this->activeTab    = 'trial_balance';
        $this->trialBalance = $this->reportRepo->getTrialBalance(
            $this->periodId ?: null,
            $this->dateFrom,
            $this->dateTo
        );
    }

    public function runProfitLoss()
    {
        $this->activeTab = 'profit_loss';
        $this->profitLoss = $this->reportRepo->getProfitAndLoss(
            $this->periodId ?: null,
            $this->dateFrom,
            $this->dateTo
        );
    }

    public function runBalanceSheet()
    {
        $this->activeTab    = 'balance_sheet';
        $this->balanceSheet = $this->reportRepo->getBalanceSheet(
            $this->periodId ?: null,
            $this->asAt
        );
    }

    public function runCashFlow()
    {
        $this->activeTab = 'cash_flow';
        $this->cashFlow  = $this->reportRepo->getCashFlow(
            $this->periodId ?: null,
            $this->dateFrom,
            $this->dateTo
        );
    }

    public function runGeneralLedger()
    {
        $this->activeTab    = 'general_ledger';
        $this->generalLedger = $this->reportRepo->getGeneralLedger(
            $this->accountId ?: null,
            $this->dateFrom,
            $this->dateTo
        );
    }

    public function generateReport()
    {
        switch ($this->activeTab) {
            case 'trial_balance':
                $this->runTrialBalance();
                break;
            case 'profit_loss':
                $this->runProfitLoss();
                break;
            case 'balance_sheet':
                $this->runBalanceSheet();
                break;
            case 'cash_flow':
                $this->runCashFlow();
                break;
            case 'general_ledger':
                $this->runGeneralLedger();
                break;
        }
        $this->success('Report generated successfully');
    }

    public function exportPdf()
    {
        $this->info('PDF export feature coming soon');
    }

    public function render()
    {
        $reportData = null;
        $totals = [];

        // Load report data based on active tab
        switch ($this->activeTab) {
            case 'trial_balance':
                $reportData = $this->trialBalance;
                if ($reportData) {
                    $totals = [
                        'debit' => collect($reportData)->sum('debit'),
                        'credit' => collect($reportData)->sum('credit'),
                    ];
                }
                break;
            case 'profit_loss':
                $reportData = $this->profitLoss;
                break;
            case 'balance_sheet':
                $reportData = $this->balanceSheet;
                break;
            case 'cash_flow':
                $reportData = $this->cashFlow;
                break;
            case 'general_ledger':
                $reportData = $this->generalLedger;
                break;
        }

        return view('livewire.accounting.financial-reports', [
            'periods'         => $this->periodRepo->getAll(),
            'accounts'        => $this->coaRepo->getPostable(),
            'costCenters'     => [], // Add cost center support if needed
            'reportData'      => $reportData,
            'totals'          => $totals,
            'startDate'       => $this->dateFrom,
            'endDate'         => $this->dateTo,
            'costCenterId'    => null,
            'selectedAccountId' => $this->accountId,
        ]);
    }
}
