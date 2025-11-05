<?php

namespace App\implementations;

use App\Interfaces\idiscountInterface;
use App\Models\Discount;

class _discountRepository implements idiscountInterface
{
    /**
     * Create a new class instance.
     */
    protected $discount;
    public function __construct(Discount $discount)
    {
        $this->discount = $discount;
    }

    public function getAll()
    {
        return $this->discount->with('tire')->get();
    }

    public function get($id)
    {
        return $this->discount->find($id);
    }

    public function create($data)
    {
        try {
            $this->discount->create($data);
            return ['status' => 'success', 'message' => 'Discount created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $this->discount->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Discount updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->discount->where('id', $id)->delete();
            return ['status' => 'success', 'message' => 'Discount deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
