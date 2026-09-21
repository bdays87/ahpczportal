<?php

namespace App\implementations;

use App\Interfaces\iaudittrailInterface;
use App\Models\AccountingAuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class _audittrailRepository implements iaudittrailInterface
{
    protected $model;

    public function __construct(AccountingAuditTrail $model)
    {
        $this->model = $model;
    }

    public function getAll($module = null, $from = null, $to = null)
    {
        return $this->model
            ->with('user')
            ->when($module, fn($q) => $q->where('module', $module))
            ->when($from,   fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,     fn($q) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->paginate(100);
    }

    public function log($module, $action, $recordType, $recordId, $reference, $oldValues, $newValues): void
    {
        try {
            $this->model->create([
                'module'      => $module,
                'action'      => $action,
                'record_type' => is_string($recordType) ? class_basename($recordType) : $recordType,
                'record_id'   => $recordId,
                'reference'   => $reference,
                'old_values'  => $oldValues ? json_encode($oldValues) : null,
                'new_values'  => $newValues ? json_encode($newValues) : null,
                'ip_address'  => Request::ip(),
                'user_id'     => Auth::id(),
            ]);
        } catch (\Exception $e) {
            // Audit log failures must never break business operations
            logger()->error('Audit trail log failed: ' . $e->getMessage());
        }
    }
}
