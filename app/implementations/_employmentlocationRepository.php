<?php

namespace App\implementations;

use App\Interfaces\iemploymentlocationInterface;
use App\Models\Employmentlocation;

class _employmentlocationRepository implements iemploymentlocationInterface
{
    /**
     * Create a new class instance.
     */
    protected $employmentlocation;
    public function __construct(Employmentlocation $employmentlocation)
    {
        $this->employmentlocation = $employmentlocation;
    }
    public function getAll()
    {
        return $this->employmentlocation->all();
    }
    public function get($id)
    {
        return $this->employmentlocation->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->employmentlocation->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Employmentlocation already exists."];
            }
            $this->employmentlocation->create($data);
            return ["status"=>"success","message"=>"Employmentlocation created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->employmentlocation->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Employmentlocation already exists."];
            }
            $this->employmentlocation->find($id)->update($data);
            return ["status"=>"success","message"=>"Employmentlocation updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->employmentlocation->find($id)->delete();
            return ["status"=>"success","message"=>"Employmentlocation deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
