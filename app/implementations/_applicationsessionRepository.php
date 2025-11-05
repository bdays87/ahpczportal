<?php

namespace App\implementations;

use App\Interfaces\iapplicationsessionInterface;
use App\Models\Applicationsession;
use App\Models\Customerapplication;

class _applicationsessionRepository implements iapplicationsessionInterface
{
    /**
     * Create a new class instance.
     */
    protected $applicationsession;
    protected $customerapplication;
    public function __construct(Applicationsession $applicationsession, Customerapplication $customerapplication)
    {
        $this->applicationsession = $applicationsession;
        $this->customerapplication = $customerapplication;
    }

    public function create($data){
        $check = $this->applicationsession->where('year', $data['year'])->first();
        if($check){
            return ['status' => 'error', 'message' => 'Application session already exists'];
        }
        try {
            $this->applicationsession->create($data);
            return ['status' => 'success', 'message' => 'Application session created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to create application session'];
        }
    }
    public function update($id,$data){
        try {
            $check = $this->applicationsession->where('id','!=', $id)->where('year', $data['year'])->first();
            if($check){
                return ['status' => 'error', 'message' => 'Application session already exists'];
            }
            $this->applicationsession->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Application session updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to update application session'];
        }
    }
    public function delete($id){
        try {
           $check =  $this->applicationsession->where('id', $id)->first();
           if(!$check){
            return ['status' => 'error', 'message' => 'Application session not found'];
           }
         $checksession =  $this->customerapplication->where('year', $check->year)->first();
         if($checksession){
            return ['status' => 'error', 'message' => 'Application session is in use'];
         }
           $check->delete();
            return ['status' => 'success', 'message' => 'Application session deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to delete application session'];
        }
    }
    public function get($id){
        return $this->applicationsession->where('id', $id)->first();
    }
    public function getall(){
        return $this->applicationsession->orderBy('year', 'desc')->get();
    }
    public function getactive(){
        return $this->applicationsession->where('year', '>=', date('Y'))->get();
    }
}
