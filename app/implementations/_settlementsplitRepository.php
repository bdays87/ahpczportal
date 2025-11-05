<?php

namespace App\implementations;

use App\Interfaces\isettlementsplitInterface;
use App\Models\Settlementsplit;

class _settlementsplitRepository implements isettlementsplitInterface
{
    /**
     * Create a new class instance.
     */
    protected $model;
    public function __construct(Settlementsplit $model)
    {
        $this->model = $model;
    }

    public function getAll(){
        return $this->model->with('employmentlocation', 'user')->get();
    }
    public function get($id){
        return $this->model->find($id);
    }
    public function create($data){
        try {
            $check = $this->model->where('employmentlocation_id', $data['employmentlocation_id'])->where('type', $data['type'])->first();
            if($check){
                return ["status"=>"error", "message"=>"Settlementsplit already exists"];
            }
            $this->model->create($data);
            return ["status"=>"success", "message"=>"Settlementsplit created successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
    public function update($id, $data){
        try {
            $check = $this->model->where('employmentlocation_id', $data['employmentlocation_id'])->where('type', $data['type'])->where('id', '!=', $id)->first();
            if($check){
                return ["status"=>"error", "message"=>"Settlementsplit already exists"];
            }
            $this->model->find($id)->update($data);
            return ["status"=>"success", "message"=>"Settlementsplit updated successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
    public function delete($id){
        try {
            $this->model->find($id)->delete();
            return ["status"=>"success", "message"=>"Settlementsplit deleted successfully"];
        } catch (\Exception $e) {
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
}
