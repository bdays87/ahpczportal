<?php

namespace App\implementations;

use App\Interfaces\iprofessionInterface;
use App\Models\Documentrequirement;
use App\Models\Profession;
use App\Models\Professioncondition;
use App\Models\Professiondocument;
use App\Models\ProfessionTire;
class _professionRepository implements iprofessionInterface
{
    /**
     * Create a new class instance.
     */
    protected $profession;
    protected $professiontire;
    protected $professiondocument;
    protected $professioncondition;
    protected $documentrequirement;
    public function __construct(Profession $profession,ProfessionTire $professiontire,Professiondocument $professiondocument,Professioncondition $professioncondition,Documentrequirement $documentrequirement)
    {
        $this->profession = $profession;
        $this->professiondocument = $professiondocument;
        $this->documentrequirement = $documentrequirement;
        $this->professioncondition = $professioncondition;
        $this->professiontire = $professiontire;
    }
    public function getAll($search,$tire_id)
    {
        return $this->profession->with('tires')->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->get();
    }
    public function create($data)
    {
        try {
            $check = $this->profession->where('name', $data['name'])->first();
            if ($check) {
                return ["status" => "error", "message" => "Profession already exists"];
            }
                  
            $this->profession->create($data);
            return ["status" => "success", "message" => "Profession created successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function get($id)
    {
        return $this->profession->with('conditions')->find($id);
    }
    public function update($id, $data)
    {
        try {
            $check = $this->profession->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status" => "error", "message" => "Profession already exists"];
            }
            $this->profession->update($data);
            return ["status" => "success", "message" => "Profession updated successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $check = $this->profession->find($id);
            if (!$check) {
                return ["status" => "error", "message" => "Profession not found"];
            }
            $check->delete();
            return ["status" => "success", "message" => "Profession deleted successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function getDocuments($id)
    {
        return $this->professiondocument->with('document','customertype')->where('profession_id', $id)->get();
    }
    public function assigndocument($id, $document_id,$customertype_id)
    {
        try {
            $check = $this->professiondocument->where('profession_id', $id)->where('document_id', $document_id)->where('customertype_id', $customertype_id)->first();
            if ($check) {
                return ["status" => "error", "message" => "Document already assigned"];
            }
            $getprofession = $this->profession->find($id);
            if(!$getprofession){
                return ["status" => "error", "message" => "Profession not found"];
            }
            $checkdocument = $this->documentrequirement->where('tire_id', $getprofession->tire_id)->where('document_id', $document_id)->first();
            if($checkdocument){
                return ["status" => "error", "message" => "Document already assigned globally for profession tire"];
            }
            $this->professiondocument->create([
                'profession_id' => $id,
                'document_id' => $document_id,
                'customertype_id' => $customertype_id,
            ]);
            return ["status" => "success", "message" => "Document assigned successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function unassigndocument($id, $document_id,$customertype_id)
    {
        try {
            $check = $this->professiondocument->where('profession_id', $id)->where('document_id', $document_id)->where('customertype_id', $customertype_id)->first();
            if (!$check) {
                return ["status" => "error", "message" => "Document not assigned"];
            }
            $check->delete();
            return ["status" => "success", "message" => "Document unassigned successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function createcondition($data){
      try {
        $this->professioncondition->create($data);
        return ["status" => "success", "message" => "Condition created successfully"];
      } catch (\Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
      }
    }
    public function updatecondition($id, $data){
      try {
        $check = $this->professioncondition->find($id);
        if (!$check) {
            return ["status" => "error", "message" => "Condition not found"];
        }
        $check->update($data);
        return ["status" => "success", "message" => "Condition updated successfully"];
      } catch (\Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
      }
    }
    public function deletecondition($id){
      try {
        $check = $this->professioncondition->find($id);
        if (!$check) {
            return ["status" => "error", "message" => "Condition not found"];
        }
        $check->delete();
        return ["status" => "success", "message" => "Condition deleted successfully"];
      } catch (\Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
      }
    }
    public function getcondition($id){
      return $this->professioncondition->find($id);
    }

    public function gettires($id){
      return $this->professiontire->with('tire')->where('profession_id', $id)->get();
    }
    public function createtire($data){
      try {
        $check = $this->professiontire->where('profession_id', $data['profession_id'])->where('tire_id', $data['tire_id'])->first();
        if ($check) {
            return ["status" => "error", "message" => "Tire already assigned"];
        }
        $this->professiontire->create($data);
        return ["status" => "success", "message" => "Tire created successfully"];
      } catch (\Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
      }
    }
    public function updatetire($id, $data){
      try {
        $check = $this->professiontire->where('profession_id', $data['profession_id'])->where('tire_id', $data['tire_id'])->where('id', '!=', $id)->first();
        if (!$check) {
            return ["status" => "error", "message" => "Tire not found"];
        }
        $check->update($data);
        return ["status" => "success", "message" => "Tire updated successfully"];
      } catch (\Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
      }

    }
    public function deletetire($id){
      try {
        $check = $this->professiontire->find($id);
        if (!$check) {
            return ["status" => "error", "message" => "Tire not found"];
        }
        $check->delete();
        return ["status" => "success", "message" => "Tire deleted successfully"];
      } catch (\Exception $e) {
        return ["status" => "error", "message" => $e->getMessage()];
      }
    }
    public function gettire($id){
      return $this->professiontire->with('tire')->find($id);
    }
}
