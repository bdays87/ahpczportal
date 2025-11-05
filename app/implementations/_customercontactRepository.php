<?php

namespace App\implementations;

use App\Interfaces\icustomercontactInterface;
use App\Models\Customercontact;

class _customercontactRepository implements icustomercontactInterface
{
    /**
     * Create a new class instance.
     */
    protected $customercontact;
    public function __construct(Customercontact $customercontact)
    {
        $this->customercontact = $customercontact;
    }

    public function create($data)
    {
        try {
            $this->customercontact->create($data);
            return ['status' => 'success', 'message' => 'Customer contact created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $this->customercontact->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Customer contact updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->customercontact->where('id', $id)->delete();
            return ['status' => 'success', 'message' => 'Customer contact deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function get($id)
    {
        try {
            return $this->customercontact->where('id', $id)->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}
