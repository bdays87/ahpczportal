<?php

namespace App\implementations;

use App\Interfaces\ipenaltyperiodInterface;
use App\Models\PenaltyPeriod;

class _penaltyperiodRepository implements ipenaltyperiodInterface
{
    protected $model;

    public function __construct(PenaltyPeriod $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('name')->get();
    }

    public function getActive()
    {
        return $this->model->where('status', 'Active')->first();
    }

    public function create($data)
    {
        try {
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Penalty period created successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $this->model->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Penalty period updated successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->model->where('id', $id)->delete();
            return ['status' => 'success', 'message' => 'Penalty period deleted successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function get($id)
    {
        return $this->model->find($id);
    }
}
