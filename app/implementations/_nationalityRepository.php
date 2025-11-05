<?php

namespace App\implementations;

use App\Interfaces\inationalityInterface;
use App\Models\Nationality;

class _nationalityRepository implements inationalityInterface
{
    /**
     * Create a new class instance.
     */
    protected $nationality;
    public function __construct(Nationality $nationality)
    {
        $this->nationality = $nationality;
    }

    public function getAll($search)
    {
        return $this->nationality->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->get();
    }

    public function get($id)
    {
        return $this->nationality->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->nationality->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Nationality already exists."];
            }
            $this->nationality->create($data);
            return ["status"=>"success","message"=>"Nationality created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->nationality->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Nationality already exists."];
            }
            $this->nationality->find($id)->update($data);
            return ["status"=>"success","message"=>"Nationality updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->nationality->find($id)->delete();
            return ["status"=>"success","message"=>"Nationality deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
