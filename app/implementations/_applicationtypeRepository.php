<?php

namespace App\implementations;

use App\Interfaces\iapplicationtypeInterface;
use App\Models\Applicationtype;

class _applicationtypeRepository implements iapplicationtypeInterface
{
    /**
     * Create a new class instance.
     */
    protected $applicationtype;
    public function __construct(Applicationtype $applicationtype)
    {
        $this->applicationtype = $applicationtype;
    }

    public function getAll()
    {
        return $this->applicationtype->all();
    }

    public function get($id)
    {
        return $this->applicationtype->find($id);
    }

    public function create($data)
    {
        try {
            $check= $this->applicationtype->where("name", $data['name'])->first();
            if ($check) {
                return ["status"=>"success","message"=>"Applicationtype already exists."];
            }
            $this->applicationtype->create($data);
            return ["status"=>"success","message"=>"Applicationtype created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $check= $this->applicationtype->where("name", $data['name'])->where("id", "!=", $id)->first();
            if ($check) {
                return ["status"=>"success","message"=>"Applicationtype already exists."];
            }
            $this->applicationtype->find($id)->update($data);
            return ["status"=>"success","message"=>"Applicationtype updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->applicationtype->find($id)->delete();
            return ["status"=>"success","message"=>"Applicationtype deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }



}
