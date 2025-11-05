<?php

namespace App\implementations;

use App\Interfaces\iqualificationcategoryInterface;
use App\Models\Qualificationcategory;

class _qualificationcategoryRepository implements iqualificationcategoryInterface
{
    /**
     * Create a new class instance.
     */
    protected $qualificationcategory;
    public function __construct(Qualificationcategory $qualificationcategory)
    {
        $this->qualificationcategory = $qualificationcategory;
    }
    public function getAll()
    {
        return $this->qualificationcategory->all();
    }
    public function get($id)
    {
        return $this->qualificationcategory->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->qualificationcategory->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Qualificationcategory already exists."];
            }
            $this->qualificationcategory->create($data);
            return ["status"=>"success","message"=>"Qualificationcategory created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->qualificationcategory->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Qualificationcategory already exists."];
            }
            $this->qualificationcategory->find($id)->update($data);
            return ["status"=>"success","message"=>"Qualificationcategory updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->qualificationcategory->find($id)->delete();
            return ["status"=>"success","message"=>"Qualificationcategory deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
