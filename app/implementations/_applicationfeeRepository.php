<?php

namespace App\implementations;

use App\Interfaces\iapplicationfeeInterface;
use App\Models\Applicationfee;

class _applicationfeeRepository implements iapplicationfeeInterface
{
    /**
     * Create a new class instance.
     */
    protected $model;
    public function __construct(Applicationfee $model)
    {
        $this->model = $model;
    }
    public function getAll($year)
    {
        return $this->model->with('tire','currency','registertype','employmentlocation')->where('year', $year)->get();
    }
    public function create($data)
    {
        try {
             $this->model->create($data);
             return ["status"=>"success", "message"=>"Applicationfee created successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error", "message"=>$th->getMessage()];
        }
    }
    public function update($id, $data)
    {
        try {
            $this->model->where('id', $id)->update($data);
            return ["status"=>"success", "message"=>"Applicationfee updated successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error", "message"=>$th->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->model->where('id', $id)->delete();
            return ["status"=>"success", "message"=>"Applicationfee deleted successfully"];
        } catch (\Throwable $th) {
            return ["status"=>"error", "message"=>$th->getMessage()];
        }
    }
    public function get($id)
    {
        return $this->model->where('id', $id)->first();
    }
}
