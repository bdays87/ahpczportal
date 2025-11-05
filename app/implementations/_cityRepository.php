<?php

namespace App\implementations;

use App\Interfaces\icityInterface;
use App\Models\City;

class _cityRepository implements icityInterface
{
    /**
     * Create a new class instance.
     */
    protected $city;
    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function getAll()
    {
        return $this->city->with('province')->get();
    }

    public function get($id)
    {
        return $this->city->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->city->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"City already exists."];
            }
            $this->city->create($data);
            return ["status"=>"success","message"=>"City created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->city->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"City already exists."];
            }
            $this->city->find($id)->update($data);
            return ["status"=>"success","message"=>"City updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->city->find($id)->delete();
            return ["status"=>"success","message"=>"City deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
