<?php

namespace App\implementations;

use App\Interfaces\iqualificationInterface;
use App\Models\Qualification;

class _qualificationRepository implements iqualificationInterface
{
    /**
     * Create a new class instance.
     */
    protected $qualification;
    public function __construct(Qualification $qualification)
    {
        $this->qualification = $qualification;
    }
    public function createQualification(array $data)
    {
        try {
            $check = $this->qualification->where('name', $data['name'])->first();
            if ($check) {
                return ['status'=>'error','message'=>'Qualification already exists.'];
            }
            $this->qualification->create($data);
            return ['status'=>'success','message'=>'Qualification created successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
    public function updateQualification(array $data,$id)
    {
        try {
            $check = $this->qualification->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ['status'=>'error','message'=>'Qualification already exists.'];
            }
            $this->qualification->find($id)->update($data);
            return ['status'=>'success','message'=>'Qualification updated successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
    }
}
    public function deleteQualification($id)
    {
        try {
            $this->qualification->find($id)->delete();
            return ['status'=>'success','message'=>'Qualification deleted successfully.'];
        } catch (\Throwable $th) {
            return ['status'=>'error','message'=>$th->getMessage()];
        }
    }
    public function getQualificationById(int $id): Qualification
    {
        return $this->qualification->find($id);
    }
    public function getAllQualifications($search = null, $profession_id)
    {
        return $this->qualification->with('institution')->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->where('profession_id', $profession_id)
        ->get();
    }
    public function getQualificationByProfessionId(int $profession_id)
    {
        return $this->qualification->with('institution')->where('profession_id', $profession_id)->get()->map(function($item){
            return [
                'id' => $item->id,
                'name' => $item->name . ' - ' . $item->institution->name,
            ];
        });
    }
    public function searchQualifications($search = null)
    {
        return $this->qualification->with('institution')->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->get();
    }
}
