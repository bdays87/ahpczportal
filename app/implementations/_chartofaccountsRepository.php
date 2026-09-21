<?php

namespace App\implementations;

use App\Interfaces\ichartofaccountsInterface;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class _chartofaccountsRepository implements ichartofaccountsInterface
{
    protected $model;
    protected $line;

    public function __construct(ChartOfAccount $model, JournalEntryLine $line)
    {
        $this->model = $model;
        $this->line  = $line;
    }

    public function getAll($search = null, $type = null, $status = null)
    {
        return $this->model
            ->with('parent', 'currency')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%"))
            ->when($type,   fn($q) => $q->where('account_type', $type))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('code')
            ->get();
    }

    public function get($id)
    {
        return $this->model->with('parent', 'children', 'currency', 'costCenter')->find($id);
    }

    public function create($data)
    {
        try {
            $exists = $this->model->where('code', $data['code'])->exists();
            if ($exists) return ['status' => 'error', 'message' => 'Account code already exists'];
            $data['createdby'] = Auth::id();
            // Derive normal balance from account type if not provided
            if (empty($data['normal_balance'])) {
                $data['normal_balance'] = in_array($data['account_type'], ['ASSET', 'EXPENSE']) ? 'DEBIT' : 'CREDIT';
            }
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Account created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $account = $this->model->find($id);
            if (!$account) return ['status' => 'error', 'message' => 'Account not found'];
            $codeExists = $this->model->where('code', $data['code'])->where('id', '!=', $id)->exists();
            if ($codeExists) return ['status' => 'error', 'message' => 'Account code already used'];
            $account->update($data);
            return ['status' => 'success', 'message' => 'Account updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $account = $this->model->find($id);
            if (!$account) return ['status' => 'error', 'message' => 'Account not found'];
            if ($account->children()->exists()) return ['status' => 'error', 'message' => 'Cannot delete account with sub-accounts'];
            if ($account->journalLines()->exists()) return ['status' => 'error', 'message' => 'Cannot delete account with journal entries'];
            $account->delete();
            return ['status' => 'success', 'message' => 'Account deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getByType($type)
    {
        return $this->model
            ->where('account_type', $type)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();
    }

    public function getPostable()
    {
        return $this->model
            ->where('allow_direct_posting', true)
            ->where('is_header', false)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();
    }

    public function getTree()
    {
        $all = $this->model->with('currency')->where('status', 'active')->orderBy('code')->get();
        return $this->buildTree($all);
    }

    public function getAccountBalance($id, $periodId = null)
    {
        $account = $this->model->find($id);
        if (!$account) return 0;
        return $account->getBalance($periodId);
    }

    public function getLedger($id, $periodId = null, $from = null, $to = null)
    {
        return $this->line
            ->with('journalEntry', 'costCenter')
            ->where('account_id', $id)
            ->whereHas('journalEntry', function ($q) use ($periodId, $from, $to) {
                $q->where('status', 'POSTED');
                if ($periodId) $q->where('accounting_period_id', $periodId);
                if ($from)     $q->whereDate('entry_date', '>=', $from);
                if ($to)       $q->whereDate('entry_date', '<=', $to);
            })
            ->orderBy('id')
            ->get();
    }

    private function buildTree($items, $parentId = null): array
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                $item->children_tree = $children;
                $branch[] = $item;
            }
        }
        return $branch;
    }
}
