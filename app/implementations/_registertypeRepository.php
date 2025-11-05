<?php

namespace App\implementations;

use App\Interfaces\iregistertypeInterface;
use App\Models\Registertype;

class _registertypeRepository implements iregistertypeInterface
{
    /**
     * Create a new class instance.
     */
    protected $registertype;
    public function __construct(Registertype $registertype)
    {
        $this->registertype = $registertype;
    }

    public function getAll()
    {
        return $this->registertype->all();
    }

    public function get($id)
    {
        return $this->registertype->find($id);
    }

    public function create($data)
    {
        try {
        $check = $this->registertype->where('name', $data['name'])->first();
        if ($check) {
            return ["status"=>"success","message"=>"Registertype already exists."];
        }
        $this->registertype->create($data);
        return ["status"=>"success","message"=>"Registertype created successfully."];
    } catch (\Throwable $th) {
        return ["status"=>"error","message"=>"Something went wrong."];
    }
    }

    public function update($id, $data)
    {
        try {
            $check = $this->registertype->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"success","message"=>"Registertype already exists."];
            }
            $this->registertype->find($id)->update($data);
            return ["status"=>"success","message"=>"Registertype updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }

    public function delete($id)
    {
        try {
            $this->registertype->find($id)->delete();
            return ["status"=>"success","message"=>"Registertype deleted successfully."];
        } catch (\Exception $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }
}
