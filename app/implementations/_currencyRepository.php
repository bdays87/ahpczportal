<?php

namespace App\implementations;

use App\Interfaces\icurrencyInterface;
use App\Models\Currency;

class _currencyRepository implements icurrencyInterface
{
    /**
     * Create a new class instance.
     */
    protected $model;
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }
    public function getAll($status)
    {
        return $this->model->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })->get();
    }
    public function get($id)
    {
        return $this->model->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->model->where('name', $data['name'])->first();
            if ($check) {
                return ['status' => 'error', 'message' => 'Currency already exists'];
            }
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Currency created successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->model->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ['status' => 'error', 'message' => 'Currency already exists'];
            }
            $this->model->find($id)->update($data);
            return ['status' => 'success', 'message' => 'Currency updated successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->model->find($id)->delete();
            return ['status' => 'success', 'message' => 'Currency deleted successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }
}
