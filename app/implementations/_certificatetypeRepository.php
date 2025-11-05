<?php

namespace App\implementations;

use App\Interfaces\icertificatetypeInterface;
use App\Models\Certificatetype;

class _certificatetypeRepository implements icertificatetypeInterface
{
    /**
     * Create a new class instance.
     */
    protected $certificatetype;
    public function __construct(Certificatetype $certificatetype)
    {
        $this->certificatetype = $certificatetype;
    }

    public function getAll()
    {
        return $this->certificatetype->all();
    }
    public function create($data){
        try{
     $check = $this->certificatetype->where('name', $data['name'])->first();
     if($check){
        return ["status"=>"error", "message"=>"Certificate Type already exists"];
     }
     $this->certificatetype->create($data);
     return ["status"=>"success", "message"=>"Certificate Type created successfully"];
    }catch(\Exception $e){
        return ["status"=>"error", "message"=>$e->getMessage()];
    }
    }
    public function update($id, $data){
        try{
            $check = $this->certificatetype->where('name', $data['name'])->where('id', '!=', $id)->first();
            if($check){
                return ["status"=>"error", "message"=>"Certificate Type already exists"];
            }
            $this->certificatetype->where('id', $id)->update($data);
            return ["status"=>"success", "message"=>"Certificate Type updated successfully"];
        }catch(\Exception $e){
            return ["status"=>"error", "message"=>$e->getMessage()];
        }
    }
    public function delete($id){
        try{
            $this->certificatetype->where('id', $id)->delete();
            return ["status"=>"success", "message"=>"Certificate Type deleted successfully"];
        }catch(\Exception $e){
            return ["status"=>"error", "message"=>$e->getMessage()];
        }

    }
}
