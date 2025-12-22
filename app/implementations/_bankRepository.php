<?php

namespace App\implementations;

use App\Interfaces\ibankInterface;
use App\Models\Bank;
use App\Models\Bankaccount;

class _bankRepository implements ibankInterface
{
    /**
     * Create a new class instance.
     */
    protected $modal;
    protected $bankaccount;
    public function __construct(Bank $modal,Bankaccount $bankaccount)
    {
        $this->modal = $modal;
        $this->bankaccount = $bankaccount;
    }
    public function getAll($search){
        return $this->modal->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->get();
    } 
    public function get($id){
        return $this->modal->where('id',$id)->first();
    } 
    public function create($data){
        try{
            $check = $this->modal->where('name',$data['name'])->first();
            if($check){
                return ['status'=>'error','message'=>'Bank already exists'];
            }
            $this->modal->create($data);
            return ['status'=>'success','message'=>'Bank created successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    } 
    public function update($id, $data){
        try{
            $check = $this->modal->where('name',$data['name'])->where('id','!=',$id)->first();
            if($check){
                return ['status'=>'error','message'=>'Bank already exists'];
            }
            $this->modal->where('id',$id)->update($data);
            return ['status'=>'success','message'=>'Bank updated successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    } 
    public function delete($id){
        try{
            $this->modal->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Bank deleted successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    } 
    public function getallaccounts(){
        return $this->bankaccount->with('bank','currency')->get();
    }

    public function getaccounts($bank_id){
        return $this->modal->with('accounts.currency')->where('id',$bank_id)->first();
    } 
    public function getaccount($id){
        return $this->bankaccount->where('id',$id)->first();
    } 
    public function createaccount($data){
        try{
            $this->bankaccount->create($data);
            return ['status'=>'success','message'=>'Bank account created successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    } 
    public function updateaccount($id, $data){
        try{
            $this->bankaccount->where('id',$id)->update($data);
            return ['status'=>'success','message'=>'Bank account updated successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    } 
    public function deleteaccount($id){
        try{
            $this->bankaccount->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Bank account deleted successfully'];
        }catch(\Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    } 
}
