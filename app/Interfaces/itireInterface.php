<?php

namespace App\Interfaces;

interface itireInterface
{
    
    public function getAll();
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getDocuments($id);
    public function assigndocument($id, $document_id,$customertype_id,$applicationtype_id);
    public function unassigndocument($id);
}
