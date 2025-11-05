<?php

namespace App\implementations;

use App\Interfaces\iprovinceInterface;
use App\Models\Province;

class _provinceRepository implements iprovinceInterface
{
    protected $province;
    public function __construct(Province $province)
    {
        $this->province = $province;
    }
    public function getAll()
    {
        return $this->province->all();
    }
    public function get($id)
    {
        return $this->province->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->province->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Province already exists."];
            }
            $this->province->create($data);
            return ["status"=>"success","message"=>"Province created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->province->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Province already exists."];
            }
            $this->province->find($id)->update($data);
            return ["status"=>"success","message"=>"Province updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->province->find($id)->delete();
            return ["status"=>"success","message"=>"Province deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
