<?php

namespace App\implementations;

use App\Interfaces\irestorationfeeInterface;
use App\Models\Restorationfee;

class _restorationfeeRepository implements irestorationfeeInterface
{
    protected $model;

    public function __construct(Restorationfee $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('name')->get();
    }

    public function create($data)
    {
        try {
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Restoration fee created successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $this->model->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Restoration fee updated successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->model->where('id', $id)->delete();
            return ['status' => 'success', 'message' => 'Restoration fee deleted successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function get($id)
    {
        return $this->model->find($id);
    }
}
