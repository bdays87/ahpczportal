<?php

namespace App\implementations;

use App\Interfaces\iqualificationlevelInterface;
use App\Models\Qualificationlevel;

class _qualificationlevelRepository implements iqualificationlevelInterface
{
    /**
     * Create a new class instance.
     */
    protected $qualificationlevel;
    public function __construct(Qualificationlevel $qualificationlevel)
    {
        $this->qualificationlevel = $qualificationlevel;
    }
    public function getAll()
    {
        return $this->qualificationlevel->all();
    }
    public function get($id)
    {
        return $this->qualificationlevel->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->qualificationlevel->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Qualificationlevel already exists."];
            }
            $this->qualificationlevel->create($data);
            return ["status"=>"success","message"=>"Qualificationlevel created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->qualificationlevel->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Qualificationlevel already exists."];
            }
            $this->qualificationlevel->find($id)->update($data);
            return ["status"=>"success","message"=>"Qualificationlevel updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->qualificationlevel->find($id)->delete();
            return ["status"=>"success","message"=>"Qualificationlevel deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
