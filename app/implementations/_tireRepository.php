<?php

namespace App\implementations;

use App\Interfaces\itireInterface;
use App\Models\Documentrequirement;
use App\Models\Tire;

class _tireRepository implements itireInterface
{
    /**
     * Create a new class instance.
     */
    protected $tire;
    protected $documentrequirement;
    public function __construct(Tire $tire,Documentrequirement $documentrequirement)
    {
        $this->tire = $tire;
        $this->documentrequirement = $documentrequirement;
    }
    public function getAll()
    {
        return $this->tire->all();
    }
    public function get($id)
    {
        return $this->tire->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->tire->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Tire already exists."];
            }
            $this->tire->create($data);
            return ["status"=>"success","message"=>"Tire created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->tire->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Tire already exists."];
            }
            $this->tire->find($id)->update($data);
            return ["status"=>"success","message"=>"Tire updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function delete($id)
    {
        try {
            $this->tire->find($id)->delete();
            return ["status"=>"success","message"=>"Tire deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
    public function getDocuments($id)
    {
        return $this->documentrequirement->with('document','customertype','applicationtype')->where('tire_id', $id)->get();
    }
    public function assigndocument($id, $document_id,$customertype_id,$applicationtype_id)
    {
        try {
            $check = $this->documentrequirement->where('tire_id', $id)->where('document_id', $document_id)->where('customertype_id', $customertype_id)->where('applicationtype_id', $applicationtype_id)->first();
            if ($check) {
                return ["status" => "error", "message" => "Document already assigned"];
            }
            $this->documentrequirement->create([
                'tire_id' => $id,
                'document_id' => $document_id,
                'customertype_id' => $customertype_id,
                'applicationtype_id' => $applicationtype_id,
            ]);
            return ["status" => "success", "message" => "Document assigned successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function unassigndocument($id)
    {
        try {
            $check = $this->documentrequirement->where('id', $id)->first();
            if (!$check) {
                return ["status" => "error", "message" => "Document not assigned"];
            }
            $check->delete();
            return ["status" => "success", "message" => "Document unassigned successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
