<?php

namespace App\implementations;

use App\Interfaces\iaudittrailInterface;
use App\Interfaces\ibudgetInterface;
use App\Models\Budget;
use App\Models\BudgetLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class _budgetRepository implements ibudgetInterface
{
    protected $model;
    protected $line;
    protected $auditRepo;

    public function __construct(Budget $model, BudgetLine $line, iaudittrailInterface $auditRepo)
    {
        $this->model     = $model;
        $this->line      = $line;
        $this->auditRepo = $auditRepo;
    }

    public function getAll($year = null, $status = null)
    {
        return $this->model
            ->with('currency', 'createdBy')
            ->when($year,   fn($q) => $q->where('year', $year))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('year')
            ->get();
    }

    public function get($id)
    {
        return $this->model->with('lines.account', 'lines.costCenter', 'currency', 'approvedBy', 'createdBy')->find($id);
    }

    public function create($data, $lines)
    {
        DB::beginTransaction();
        try {
            $data['createdby'] = Auth::id();
            $data['status']    = 'DRAFT';
            $budget = $this->model->create($data);
            $this->saveLines($budget->id, $lines);
            $this->auditRepo->log('BUDGET', 'CREATE', Budget::class, $budget->id, $budget->name, null, $data);
            DB::commit();
            return ['status' => 'success', 'message' => 'Budget created successfully', 'data' => $budget->id];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data, $lines)
    {
        DB::beginTransaction();
        try {
            $budget = $this->model->find($id);
            if (!$budget) return ['status' => 'error', 'message' => 'Budget not found'];
            if (!in_array($budget->status, ['DRAFT'])) return ['status' => 'error', 'message' => 'Only DRAFT budgets can be edited'];
            $old = $budget->toArray();
            $budget->update($data);
            $this->line->where('budget_id', $id)->delete();
            $this->saveLines($id, $lines);
            $this->auditRepo->log('BUDGET', 'UPDATE', Budget::class, $id, $budget->name, $old, $data);
            DB::commit();
            return ['status' => 'success', 'message' => 'Budget updated successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $budget = $this->model->find($id);
            if (!$budget) return ['status' => 'error', 'message' => 'Budget not found'];
            if ($budget->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT budgets can be deleted'];
            $this->line->where('budget_id', $id)->delete();
            $budget->delete();
            $this->auditRepo->log('BUDGET', 'DELETE', Budget::class, $id, $budget->name, null, null);
            DB::commit();
            return ['status' => 'success', 'message' => 'Budget deleted successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function approve($id)
    {
        try {
            $budget = $this->model->find($id);
            if (!$budget) return ['status' => 'error', 'message' => 'Budget not found'];
            if ($budget->status !== 'DRAFT') return ['status' => 'error', 'message' => 'Only DRAFT budgets can be approved'];
            $budget->update([
                'status'      => 'APPROVED',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            $this->auditRepo->log('BUDGET', 'APPROVE', Budget::class, $id, $budget->name, null, ['status' => 'APPROVED']);
            return ['status' => 'success', 'message' => 'Budget approved successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getBudgetVsActual($id)
    {
        $budget = $this->model->with('lines.account')->find($id);
        if (!$budget) return [];

        $report = [];
        foreach ($budget->lines as $line) {
            $report[] = [
                'account'        => $line->account?->name,
                'account_code'   => $line->account?->code,
                'month'          => $line->month,
                'budgeted'       => $line->budgeted_amount,
                'actual'         => $line->actual_amount,
                'variance'       => $line->actual_amount - $line->budgeted_amount,
                'variance_pct'   => $line->budgeted_amount != 0
                    ? round(($line->actual_amount - $line->budgeted_amount) / $line->budgeted_amount * 100, 2)
                    : null,
            ];
        }
        return $report;
    }

    private function saveLines(int $budgetId, array $lines): void
    {
        foreach ($lines as $line) {
            $this->line->create([
                'budget_id'      => $budgetId,
                'account_id'     => $line['account_id'],
                'cost_center_id' => $line['cost_center_id'] ?? null,
                'month'          => $line['month'],
                'budgeted_amount' => $line['budgeted_amount'] ?? 0,
                'actual_amount'  => 0,
                'variance'       => 0,
            ]);
        }
    }
}
