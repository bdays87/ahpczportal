<?php

namespace App\Livewire\Admin;

use App\Models\Customerprofession;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class SageSyncStatus extends Component
{
    use WithPagination, Toast;

    public $activeTab = 'customers';
    public $filterStatus = 'all';
    public $search = '';
    public $selectedCustomers = [];
    public $selectedInvoices = [];
    public $selectAll = false;

    public $breadcrumbs = [];

    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Sage Sync Status',
            ],
        ];
    }

    public function getCustomersProperty()
    {
        $query = Customerprofession::with(['customer', 'profession'])
            ->where('status', 'APPROVED')
            ->whereNotNull('sage_sync_status');

        if ($this->filterStatus !== 'all') {
            $query->where('sage_sync_status', strtoupper($this->filterStatus));
        }

        if ($this->search) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('surname', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('sage_synced_at', 'desc')
                     ->paginate(20);
    }

    public function getInvoicesProperty()
    {
        $query = Invoice::with(['customer'])
            ->where('status', 'PAID')
            ->whereNotNull('sage_sync_status');

        if ($this->filterStatus !== 'all') {
            $query->where('sage_sync_status', strtoupper($this->filterStatus));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('sage_invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('surname', 'like', "%{$this->search}%");
                  });
            });
        }

        return $query->orderBy('sage_synced_at', 'desc')
                     ->paginate(20);
    }

    public function getStatsProperty()
    {
        return [
            'customers' => [
                'pending' => Customerprofession::where('status', 'APPROVED')
                    ->whereIn('sage_sync_status', [null, 'PENDING'])
                    ->count(),
                'synced' => Customerprofession::where('sage_sync_status', 'SYNCED')->count(),
                'failed' => Customerprofession::where('sage_sync_status', 'FAILED')->count(),
            ],
            'invoices' => [
                'pending' => Invoice::where('status', 'PAID')
                    ->whereIn('sage_sync_status', [null, 'PENDING'])
                    ->count(),
                'synced' => Invoice::where('sage_sync_status', 'SYNCED')->count(),
                'failed' => Invoice::where('sage_sync_status', 'FAILED')->count(),
            ],
            'statements' => [
                'total_balance' => DB::table('customer_sage_statements')->sum('current_balance'),
                'customers_with_balance' => DB::table('customer_sage_statements')
                    ->where('current_balance', '>', 0)
                    ->count(),
            ],
        ];
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function retrySyncCustomer($professionId)
    {
        $profession = Customerprofession::find($professionId);
        
        if ($profession) {
            $profession->update([
                'sage_sync_status' => 'PENDING',
                'sage_sync_error' => null,
            ]);
            
            $this->success('Customer marked for retry. Connector will pick it up on next poll.');
        }
    }

    public function retrySyncInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        
        if ($invoice) {
            $invoice->update([
                'sage_sync_status' => 'PENDING',
                'sage_sync_error' => null,
            ]);
            
            $this->success('Invoice marked for retry. Connector will pick it up on next poll.');
        }
    }

    /**
     * Push selected customers to Sage (mark as PENDING for connector to pick up)
     */
    public function pushSelectedCustomers()
    {
        if (empty($this->selectedCustomers)) {
            $this->error('No customers selected');
            return;
        }

        $count = Customerprofession::whereIn('id', $this->selectedCustomers)
            ->update([
                'sage_sync_status' => 'PENDING',
                'sage_sync_error' => null,
            ]);

        $this->selectedCustomers = [];
        $this->selectAll = false;
        
        $this->success("$count customer(s) queued for sync. Connector will process on next poll.");
    }

    /**
     * Push ALL approved customers with approved applications
     */
    public function pushAllCustomers()
    {
        $count = Customerprofession::where('status', 'APPROVED')
            ->whereHas('applications', function ($query) {
                $query->where('status', 'APPROVED');
            })
            ->whereHas('registration', function ($query) {
                $query->where('status', 'APPROVED');
            })
            ->whereIn('sage_sync_status', [null, 'FAILED'])
            ->update([
                'sage_sync_status' => 'PENDING',
                'sage_sync_error' => null,
            ]);

        $this->success("$count approved customer(s) queued for sync. Connector will process on next poll.");
    }

    /**
     * Push selected invoices to Sage (mark as PENDING)
     */
    public function pushSelectedInvoices()
    {
        if (empty($this->selectedInvoices)) {
            $this->error('No invoices selected');
            return;
        }

        $count = Invoice::whereIn('id', $this->selectedInvoices)
            ->where('status', 'PAID')
            ->update([
                'sage_sync_status' => 'PENDING',
                'sage_sync_error' => null,
            ]);

        $this->selectedInvoices = [];
        $this->selectAll = false;
        
        $this->success("$count invoice(s) queued for sync. Connector will process on next poll.");
    }

    /**
     * Push ALL paid invoices (only for customers already synced to Sage)
     */
    public function pushAllInvoices()
    {
        $count = Invoice::where('status', 'PAID')
            ->whereHas('customer.customerprofessions', function ($query) {
                $query->where('sage_sync_status', 'SYNCED');
            })
            ->whereIn('sage_sync_status', [null, 'FAILED'])
            ->update([
                'sage_sync_status' => 'PENDING',
                'sage_sync_error' => null,
            ]);

        $this->success("$count paid invoice(s) queued for sync. Connector will process on next poll.");
    }

    /**
     * Retry all failed syncs
     */
    public function retryAllFailed()
    {
        if ($this->activeTab === 'customers') {
            $count = Customerprofession::where('sage_sync_status', 'FAILED')
                ->update([
                    'sage_sync_status' => 'PENDING',
                    'sage_sync_error' => null,
                ]);
            
            $this->success("$count failed customer(s) queued for retry.");
        } else {
            $count = Invoice::where('sage_sync_status', 'FAILED')
                ->update([
                    'sage_sync_status' => 'PENDING',
                    'sage_sync_error' => null,
                ]);
            
            $this->success("$count failed invoice(s) queued for retry.");
        }
    }

    public function render()
    {
        return view('livewire.admin.sage-sync-status', [
            'stats' => $this->stats,
            'customers' => $this->activeTab === 'customers' ? $this->customers : collect(),
            'invoices' => $this->activeTab === 'invoices' ? $this->invoices : collect(),
        ]);
    }
}
