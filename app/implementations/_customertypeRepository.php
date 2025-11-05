<?php

namespace App\implementations;

use App\Interfaces\icustomertypeInterface;
use App\Models\Customertype;
use App\Models\CustomertypeRegistertype;

class _customertypeRepository implements icustomertypeInterface
{
    /**
     * Create a new class instance.
     */
    protected $customertype;
    protected $customertypeRegistertype;
    public function __construct(Customertype $customertype, CustomertypeRegistertype $customertypeRegistertype)
    {
        $this->customertype = $customertype;
        $this->customertypeRegistertype = $customertypeRegistertype;
    }

    public function getAll()
    {
        return $this->customertype->with('registertypes.registertype')->get();
    }

    public function get($id)
    {
        return $this->customertype->with('registertypes.registertype')->find($id);
    }

    public function create($data)
    {
        try {
            $check = $this->customertype->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Customertype already exists."];
            }
            $this->customertype->create($data);
            return ["status"=>"success","message"=>"Customertype created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $check = $this->customertype->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Customertype already exists."];
            }
            $this->customertype->find($id)->update($data);
            return ["status"=>"success","message"=>"Customertype updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->customertype->find($id)->delete();
            return ["status"=>"success","message"=>"Customertype deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function assignregistertype($id, $registertype_id)
    {
        try {
        $check = $this->customertypeRegistertype->where('customertype_id', $id)->where('registertype_id', $registertype_id)->first();
        if ($check) {
            return ["status"=>"error","message"=>"Registertype already assigned."];
        }
        $this->customertypeRegistertype->create([
            'customertype_id' => $id,
            'registertype_id' => $registertype_id
        ]);
        return ["status"=>"success","message"=>"Registertype assigned successfully."];
    } catch (\Throwable $th) {
        return ["status"=>"error","message"=>"Something went wrong."];
    }
    }

    public function removeregistertype($id, $registertype_id)
    {
        try {
        $check = $this->customertypeRegistertype->where('customertype_id', $id)->where('registertype_id', $registertype_id)->first();
        if (!$check) {
            return ["status"=>"error","message"=>"Registertype not assigned."];
        }
        $this->customertypeRegistertype->where('customertype_id', $id)->where('registertype_id', $registertype_id)->delete();
        return ["status"=>"success","message"=>"Registertype removed successfully."];
    } catch (\Throwable $th) {
        return ["status"=>"error","message"=>"Something went wrong."];  
    }
}

public function getregistertypes($id){
    $registertypes = $this->customertypeRegistertype->with('registertype')->where('customertype_id', $id)->get();
    return $registertypes->map(function ($registertype){
        return [
            'id' => $registertype->registertype->id,
            'name' => $registertype->registertype->name
        ];
    });
}

}
