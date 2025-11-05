<?php

namespace App\implementations;

use App\Interfaces\iotherserviceInterface;
use App\Models\Otherservice;
use App\Models\Otherservicedocument;

class _otherserviceRepository implements iotherserviceInterface
{
    /**
     * Create a new class instance.
     */
    protected $modal;
    protected $otherserviceDocument;
    public function __construct(Otherservice $modal,Otherservicedocument $otherserviceDocument)
    {
        $this->modal = $modal;
        $this->otherserviceDocument = $otherserviceDocument;
    }
    public function getAll($search){
         return $this->modal->with('currency')->when($search,function($query)use($search){
            $query->where('name', 'like', "%{$search}%");
         })->get();
    }
    public function get($id){
        return $this->modal->where("id",$id)->first();

    }
    public function create($data){

        try{
            $check = $this->modal->where("name",$data["name"])->first();
            if($check){
                return ['status'=>'error','message'=>'Name already taken'];
            }
            $this->modal->create($data);
            return ['status'=>'success','message'=>'Other service successfully created'];

        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }

    }
    public function update($id, $data){

        try{
            $check = $this->modal->where("name",$data["name"])->where("id","!=",$id)->first();
            if($check){
                return ['status'=>'error','message'=>'Name already taken'];
            }
            $this->modal->where("id",$id)->update($data);
            return ['status'=>'success','message'=>'Other service successfully created'];

        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }

    }
    public function delete($id){

        try{
            $check = $this->modal->where("id",$id)->first();
            if(!$check){
                return ['status'=>'error','message'=>'other service not found'];
            }
            $check->delete();
            return ['status'=>'success','message'=>'Other service successfully created'];

        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }


    }

    public function getdocuments($id){
        return $this->modal->with('documents')->where("id",$id)->first();

    }
    public function createdocument($id, $document_id){
        try{
            $check = $this->otherserviceDocument->where("otherservice_id",$id)->where("document_id",$document_id)->first();
            if($check){
                return ['status'=>'error','message'=>'Document already added'];
            }
            $this->otherserviceDocument->create([
                "otherservice_id"=>$id,
                "document_id"=>$document_id
            ]);
            return ['status'=>'success','message'=>'Document successfully created'];

        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }

    }
    
    public function deletedocument($id,$document_id){
        try{
            $check = $this->otherserviceDocument->where("otherservice_id",$id)->where("document_id",$document_id)->first();
            if(!$check){
                return ['status'=>'error','message'=>'Document not found'];
            }
            $check->delete();
            return ['status'=>'success','message'=>'Document successfully deleted'];

        }catch(\Throwable $e){
            return ["status"=>"error","message"=>$e->getMessage()];
        }

    }
}
