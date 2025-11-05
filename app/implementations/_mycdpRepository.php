<?php

namespace App\implementations;

use App\Interfaces\imycdpInterface;
use App\Models\Mycdp;
use App\Models\Mycdpattachment;
use Illuminate\Support\Facades\Storage;

class _mycdpRepository implements imycdpInterface
{
    /**
     * Create a new class instance.
     */
    protected $mycdp;
    protected $mycdpattachment;
    public function __construct(Mycdp $mycdp,Mycdpattachment $mycdpattachment)
    {
        $this->mycdp = $mycdp;
        $this->mycdpattachment = $mycdpattachment;
    }
    public function create($data){
     try{
        $this->mycdp->create($data);
        return ["status"=>"success","message"=>"Mycdp created successfully"];
     }catch(\Exception $e){
        return ["status"=>"error","message"=>$e->getMessage()];
     }   
        
    }
    public function update($id,$data){
        try{
             $check = $this->mycdp->find($id);
             if($check){
                $check->update($data);
                return ["status"=>"success","message"=>"Mycdp updated successfully"];
             }
             return ["status"=>"error","message"=>"Mycdp not found"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }   
    }
    public function delete($id){
        try{
             $check = $this->mycdp->find($id);
             if($check && $check->status == "PENDING"){
                $check->delete();
                return ["status"=>"success","message"=>"Mycdp deleted successfully"];
             }
             return ["status"=>"error","message"=>"Mycdp not found or not in pending status"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }   
    }
    public function get($id){
             $check = $this->mycdp->with([
                'attachments',
                'customerprofession.customer',
                'customerprofession.profession'
             ])->where('id',$id)->first();
             return $check;
    }
    public function savesassessment($data){
        try{
             $check = $this->mycdp->find($data['id']);
             if($check){
                $check->update($data);
                return ["status"=>"success","message"=>"Mycdp assessment saved successfully"];
             }
             return ["status"=>"error","message"=>"Mycdp not found"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }   
    }
    public function saveattachment($data){
        try{
            $this->mycdpattachment->create($data);
            return ["status"=>"success","message"=>"Mycdp attachment saved successfully"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }   
    }
    public function deleteattachment($id){
        try{
            $check = $this->mycdpattachment->find($id);
            if($check){
                Storage::delete($check->file);
                $check->delete();
                return ["status"=>"success","message"=>"Mycdp attachment deleted successfully"];
            }
            return ["status"=>"error","message"=>"Mycdp attachment not found"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }   
    }
    public function getbycustomerprofession($id,$year){
  
           return $this->mycdp->with('attachments')->where('customerprofession_id',$id)->where('year',$year)->get();
      
    }
    public function submitforassessment($id){
        try{
             $check = $this->mycdp->find($id);
             if($check){
                $check->update(['status'=>'AWAITING']);
                return ["status"=>"success","message"=>"Mycdp submitted for assessment successfully"];
             }
             return ["status"=>"error","message"=>"Mycdp not found"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }   
    }

    public function getcdps($year,$status){
        return $this->mycdp->with('customerprofession.profession','customerprofession.customer','customerprofession.customertype','customerprofession.registertype')->where('year',$year)->where('status',$status)->get();
    }
    public function assignpoints($id,$points){
        try{
            $check = $this->mycdp->find($id);
            if($check){
                $check->update(['points'=>$points['points'],'comment'=>$points['comment'],"status"=>"PROCESSED"]);
                return ["status"=>"success","message"=>"Mycdp points assigned successfully"];
            
            }
            return ["status"=>"error","message"=>"Mycdp not found"];
        }catch(\Exception $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }
    }
}
