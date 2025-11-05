<?php

namespace App\implementations;

use App\Interfaces\iregistrationfeeInterface;
use App\Models\Registrationfee;

class _registrationfeeRepository implements iregistrationfeeInterface
{
    /**
     * Create a new class instance.
     */
    protected $registrationfee;
    public function __construct(Registrationfee $registrationfee)
    {
        $this->registrationfee = $registrationfee;
    }

    public function getAll($year){
        return $this->registrationfee->with('currency','customertype','employmentlocation')->where('year', $year)->get();
    }
    public function get($id){
        return $this->registrationfee->find($id);
    }
    public function create($data){
        try {
            $check = $this->registrationfee->where('customertype_id', $data['customertype_id'])->where('year', $data['year'])->where('employmentlocation_id', $data['employmentlocation_id'])->first();
            if($check){
                return [
                    'status'=>'error',
                    'message'=>'Registration Fee Already Exists'
                ];
            }
            $this->registrationfee->create($data);
            return [
                'status'=>'success',
                'message'=>'Registration Fee Created Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }
    }
    public function update($id, $data){
        try {
            $this->registrationfee->where('id', $id)->update($data);
            return [
                'status'=>'success',
                'message'=>'Registration Fee Updated Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }
    }
    public function delete($id){
        try {
            $this->registrationfee->where('id', $id)->delete();
            return [
                'status'=>'success',
                'message'=>'Registration Fee Deleted Successfully'
            ];
        } catch (\Throwable $th) {
            return [
                'status'=>'error',
                'message'=>$th->getMessage()
            ];
        }
    }
}
