<?php

namespace App\Livewire\Accounting;

use App\Interfaces\iaudittrailInterface;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class AuditTrail extends Component
{
    use Toast, WithPagination;

    public $breadcrumbs  = [];
    public $search       = '';
    public $filterModule = '';
    public $filterAction = '';
    public $filterEntity = '';
    public $filterFrom;
    public $filterTo;
    public $viewModal    = false;
    public $selectedLog;

    protected $auditRepo;

    public function boot(iaudittrailInterface $auditRepo)
    {
        $this->auditRepo = $auditRepo;
    }

    public function mount()
    {
        $this->filterFrom = date('Y-m-01');
        $this->filterTo   = date('Y-m-d');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Audit Trail'],
        ];
    }

    public function getLogs()
    {
        $query = $this->auditRepo->getAll($this->filterModule ?: null, $this->filterFrom, $this->filterTo);
        
        // Apply additional filters if the repo returns a collection
        if ($this->filterAction) {
            $query = $query->where('action', $this->filterAction);
        }
        
        if ($this->filterEntity) {
            $query = $query->where('record_type', $this->filterEntity);
        }
        
        if ($this->search) {
            $query = $query->filter(function($log) {
                return stripos($log->reference, $this->search) !== false ||
                       stripos($log->user?->name, $this->search) !== false;
            });
        }
        
        return $query;
    }

    public function viewLog($id)
    {
        // Fetch directly from repo instead of filtering paginated results
        $allLogs = \App\Models\AccountingAuditTrail::with('user')->find($id);
        $this->selectedLog = $allLogs;
        $this->viewModal   = true;
    }

    public function modules(): array
    {
        return ['JOURNAL', 'AP', 'AR', 'COA', 'BUDGET', 'TAX', 'PERIOD'];
    }

    public function headers(): array
    {
        return [
            ['key' => 'created_at',  'label' => 'Timestamp'],
            ['key' => 'module',      'label' => 'Module'],
            ['key' => 'action',      'label' => 'Action'],
            ['key' => 'record_type', 'label' => 'Record'],
            ['key' => 'reference',   'label' => 'Reference'],
            ['key' => 'user.name',   'label' => 'User'],
            ['key' => 'action_btn',  'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.audit-trail', [
            'logs'    => $this->getLogs(),
            'headers' => $this->headers(),
            'modules' => $this->modules(),
        ]);
    }
}
