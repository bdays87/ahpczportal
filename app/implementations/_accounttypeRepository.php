<?php

namespace App\implementations;

use App\Interfaces\iaccounttypeInterface;
use App\Models\Accounttype;

class _accounttypeRepository implements iaccounttypeInterface
{
    /**
     * Create a new class instance.
     */
    protected $accounttype;
    public function __construct(Accounttype $accounttype)
    {
        $this->accounttype = $accounttype;
    }

    public function getAll()
    {
        return $this->accounttype->all();
    }

    public function getsystemmodules($id)
    {
        return $this->accounttype->with('systemmodules')->where('id', $id)->first();
    }

    public function get($id)
    {
        return $this->accounttype->find($id);
    }

    public function create($data)
    {
        try {
            $check = $this->accounttype->where('name', $data['name'])->first();
            if ($check) {
                return ['status' => 'error', 'message' => 'Account type already exists'];
            }
            $this->accounttype->create($data);
            return ['status' => 'success', 'message' => 'Account type created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $check = $this->accounttype->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ['status' => 'error', 'message' => 'Account type already exists'];
            }
            $this->accounttype->find($id)->update($data);
            return ['status' => 'success', 'message' => 'Account type updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->accounttype->find($id)->delete();
            return ['status' => 'success', 'message' => 'Account type deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
