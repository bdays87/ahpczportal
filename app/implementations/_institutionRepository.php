<?php

namespace App\implementations;

use App\Interfaces\institutionInterface;
use App\Models\Institution;
class _institutionRepository implements institutionInterface
{
    protected $modal;
    public function __construct(Institution $modal)
    {
        $this->modal = $modal;
    }
    public function getAll($search=null)
    {
        return $this->modal->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->paginate(20);
    }
    public function getAllOptions()
    {
        return $this->modal->get();
     
    }
    public function get($id)
    {
        return $this->modal->where('id',$id)->first();
    }
    public function create($data)
    {
        try{
            $check = $this->modal->where('name',$data['name'])->first();
            if($check){
                return ['status'=>'error','message'=>'Institution already exists'];
            }
            $this->modal->create($data);
            return ['status'=>'success','message'=>'Institution created successfully'];
        } catch (\Exception $e) {
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public function update($id, $data)
    {
        try{
            $check = $this->modal->where('name',$data['name'])->where('id','!=',$id)->first();
            if($check){
                return ['status'=>'error','message'=>'Institution already exists'];
            }
            $this->modal->where('id',$id)->update($data);
            return ['status'=>'success','message'=>'Institution updated successfully'];
        } catch (\Exception $e) {
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public function delete($id)
    {
        try{
            $this->modal->where('id',$id)->delete();
            return ['status'=>'success','message'=>'Institution deleted successfully'];
        } catch (\Exception $e) {
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
}
