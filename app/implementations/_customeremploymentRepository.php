<?php

namespace App\implementations;

use App\Interfaces\icustomeremploymentInteface;
use App\Models\Customeremployment;

class _customeremploymentRepository implements icustomeremploymentInteface
{
    /**
     * Create a new class instance.
     */
    protected $customeremployment;
    public function __construct(Customeremployment $customeremployment)
    {
        $this->customeremployment = $customeremployment;
    }

    public function create($data)
    {
        try {
            $check = $this->customeremployment->where('customer_id', $data['customer_id'])->where('end_date', 'CURRENT')->first();
            if($check){
                return ['status' => 'error', 'message' => 'Customer already has a current employment'];
            }
            $this->customeremployment->create($data);
            return ['status' => 'success', 'message' => 'Customer employment created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $check = $this->customeremployment->where('customer_id', $data['customer_id'])->where('end_date', 'CURRENT')->first();
            if($check){
                return ['status' => 'error', 'message' => 'Customer already has a current employment'];
            }
            $this->customeremployment->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Customer employment updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->customeremployment->where('id', $id)->delete();
            return ['status' => 'success', 'message' => 'Customer employment deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function get($id)
    {
        try {
            return $this->customeremployment->where('id', $id)->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}
