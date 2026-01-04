<?php

namespace App\implementations;

use App\Interfaces\idocumentInterface;
use App\Models\Document;

class _documentRepository implements idocumentInterface
{
    /**
     * Create a new class instance.
     */
    protected $document;
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function getAll($search)
    {
        return $this->document->when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        })->get();
    }

    public function get($id)
    {
        return $this->document->find($id);
    }

    public function create($data)
    {
        try {
            $check = $this->document->where('name', $data['name'])->first();
            if ($check) {
                return ["status"=>"error","message"=>"Document already exists."];
            }
            $this->document->create($data);
            return ["status"=>"success","message"=>"Document created successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $check = $this->document->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status"=>"error","message"=>"Document already exists."];
            }
            $this->document->find($id)->update($data);
            return ["status"=>"success","message"=>"Document updated successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }

    public function delete($id)
    {
        try {
            $this->document->find($id)->delete();
            return ["status"=>"success","message"=>"Document deleted successfully."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>"Something went wrong."];
        }
    }
}
