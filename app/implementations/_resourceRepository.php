<?php

namespace App\implementations;

use App\Interfaces\iresourceInterface;
use App\Models\Resource;

class _resourceRepository implements iresourceInterface
{
    protected $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getAll($search)
    {
        return $this->resource->when($search, function ($query) use ($search) {
            $query->where('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
        }, function ($query) {
            return $query;
        })->latest()->get();
    }

    public function get($id)
    {
        return $this->resource->find($id);
    }

    public function create($data)
    {
        try {
            $this->resource->create($data);

            return ['status' => 'success', 'message' => 'Resource created successfully.'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $this->resource->find($id)->update($data);

            return ['status' => 'success', 'message' => 'Resource updated successfully.'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => 'Something went wrong.'];
        }
    }

    public function delete($id)
    {
        try {
            $resource = $this->resource->find($id);
            if ($resource) {
                // Delete the file from storage
                if ($resource->file_path && \Storage::disk('public')->exists($resource->file_path)) {
                    \Storage::disk('public')->delete($resource->file_path);
                }
                $resource->delete();
            }

            return ['status' => 'success', 'message' => 'Resource deleted successfully.'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => 'Something went wrong.'];
        }
    }

    public function getActiveResources($search)
    {
        return $this->resource->where('is_active', true)
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            }, function ($query) {
                return $query;
            })
            ->latest()
            ->get();
    }

    public function getLatest($limit)
    {
        return $this->resource->where('is_active', true)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
