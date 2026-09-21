<?php

namespace App\implementations;

use App\Interfaces\iaccountingperiodInterface;
use App\Models\AccountingPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class _accountingperiodRepository implements iaccountingperiodInterface
{
    protected $model;

    public function __construct(AccountingPeriod $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('createdBy')->orderByDesc('year')->orderByDesc('month')->get();
    }

    public function getOpen()
    {
        return $this->model->where('status', 'OPEN')->orderBy('year')->orderBy('month')->get();
    }

    public function get($id)
    {
        return $this->model->with('createdBy')->find($id);
    }

    public function create($data)
    {
        try {
            $data['createdby'] = Auth::id();
            $existing = $this->model
                ->where('month', $data['month'])
                ->where('year', $data['year'])
                ->first();
            if ($existing) {
                return ['status' => 'error', 'message' => 'A period for this month and year already exists'];
            }
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Accounting period created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $period = $this->model->find($id);
            if (!$period) return ['status' => 'error', 'message' => 'Period not found'];
            if ($period->status !== 'OPEN') return ['status' => 'error', 'message' => 'Only OPEN periods can be updated'];
            $period->update($data);
            return ['status' => 'success', 'message' => 'Accounting period updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $period = $this->model->find($id);
            if (!$period) return ['status' => 'error', 'message' => 'Period not found'];
            if ($period->status !== 'OPEN') return ['status' => 'error', 'message' => 'Only OPEN periods can be deleted'];
            if ($period->journalEntries()->exists()) {
                return ['status' => 'error', 'message' => 'Cannot delete period with posted journal entries'];
            }
            $period->delete();
            return ['status' => 'success', 'message' => 'Accounting period deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function close($id)
    {
        try {
            $period = $this->model->find($id);
            if (!$period) return ['status' => 'error', 'message' => 'Period not found'];
            if ($period->status !== 'OPEN') return ['status' => 'error', 'message' => 'Period is not open'];
            $period->update([
                'status'     => 'CLOSED',
                'closed_by'  => Auth::id(),
                'closed_at'  => now(),
            ]);
            return ['status' => 'success', 'message' => 'Period closed successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function lock($id)
    {
        try {
            $period = $this->model->find($id);
            if (!$period) return ['status' => 'error', 'message' => 'Period not found'];
            if ($period->status !== 'CLOSED') return ['status' => 'error', 'message' => 'Only CLOSED periods can be locked'];
            $period->update(['status' => 'LOCKED']);
            return ['status' => 'success', 'message' => 'Period locked successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getCurrentPeriod()
    {
        $now = Carbon::now();
        return $this->model
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->where('status', 'OPEN')
            ->first();
    }

    public function getPeriodByMonthYear($month, $year)
    {
        return $this->model->where('month', $month)->where('year', $year)->first();
    }
}
