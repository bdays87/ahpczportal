<?php

namespace App\implementations;

use App\Interfaces\icostcenterInterface;
use App\Models\CostCenter;
use Illuminate\Support\Facades\Auth;

class _costcenterRepository implements icostcenterInterface
{
    protected $model;

    public function __construct(CostCenter $model)
    {
        $this->model = $model;
    }

    public function getAll($status = null)
    {
        return $this->model
            ->with('parent')
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('code')
            ->get();
    }

    public function get($id)
    {
        return $this->model->with('parent', 'children')->find($id);
    }

    public function create($data)
    {
        try {
            $exists = $this->model->where('code', $data['code'])->exists();
            if ($exists) return ['status' => 'error', 'message' => 'A cost center with this code already exists'];
            $data['createdby'] = Auth::id();
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Cost center created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $cc = $this->model->find($id);
            if (!$cc) return ['status' => 'error', 'message' => 'Cost center not found'];
            $exists = $this->model->where('code', $data['code'])->where('id', '!=', $id)->exists();
            if ($exists) return ['status' => 'error', 'message' => 'Code already used by another cost center'];
            $cc->update($data);
            return ['status' => 'success', 'message' => 'Cost center updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $cc = $this->model->find($id);
            if (!$cc) return ['status' => 'error', 'message' => 'Cost center not found'];
            if ($cc->children()->exists()) return ['status' => 'error', 'message' => 'Cannot delete cost center with child centers'];
            if ($cc->journalLines()->exists()) return ['status' => 'error', 'message' => 'Cannot delete cost center with journal entries'];
            $cc->delete();
            return ['status' => 'success', 'message' => 'Cost center deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getTree()
    {
        $all = $this->model->where('status', 'active')->orderBy('code')->get();
        return $this->buildTree($all);
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
