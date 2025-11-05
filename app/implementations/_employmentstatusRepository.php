<?php

namespace App\implementations;

use App\Interfaces\iemploymentstatusInterface;
use App\Models\Employmentstatus;

class _employmentstatusRepository implements iemploymentstatusInterface
{
    /**
     * Create a new class instance.
     */
    protected $employmentstatus;
    public function __construct(Employmentstatus $employmentstatus)
    {
        $this->employmentstatus = $employmentstatus;
    }
    public function getAll()
    {
        return $this->employmentstatus->all();
    }
    public function get($id)
    {
        return $this->employmentstatus->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->employmentstatus->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Employmentstatus already exists."];
            }
            $this->employmentstatus->create($data);
            return ["status"=>"success","message"=>"Employmentstatus created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->employmentstatus->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Employmentstatus already exists."];
            }
            $this->employmentstatus->find($id)->update($data);
            return ["status"=>"success","message"=>"Employmentstatus updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->employmentstatus->find($id)->delete();
            return ["status"=>"success","message"=>"Employmentstatus deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
