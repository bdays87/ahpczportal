<?php

namespace App\implementations;

use App\Interfaces\istudentInterface;
use App\Models\Studentplacement;
use App\Models\Studentqualification;

class _studentRepository implements istudentInterface
{
    /**
     * Create a new class instance.
     */
    protected $studentqualification;
    protected $studentplacement;
    public function __construct(Studentqualification $studentqualification,Studentplacement $studentplacement)
    {
        $this->studentqualification = $studentqualification;
        $this->studentplacement = $studentplacement;
    }
    public function createqualification($data){
        try {
            $this->studentqualification->create($data);
            return ['status'=>'success','message'=>'Qualification created successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
    public function getqualification($id){
     
          return $this->studentqualification->find($id);
       
    }
    public function updatequalification($id, $data){
        try {
            $this->studentqualification->where('id',$id)->update($data);
            return ['status'=>'success','message'=>'Qualification updated successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
    public function deletequalification($id){
        try {
            $this->studentqualification->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Qualification deleted successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }

    public function createplacement($data){
        try {
            $this->studentplacement->create($data);
            return ['status'=>'success','message'=>'Placement created successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
    public function getplacement($id){
      
            return $this->studentplacement->find($id);
       
    }
    public function updateplacement($id, $data){
        try {
            $this->studentplacement->where('id',$id)->update($data);
            return ['status'=>'success','message'=>'Placement updated successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
    public function deleteplacement($id){
        try {
            $this->studentplacement->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Placement deleted successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
}
