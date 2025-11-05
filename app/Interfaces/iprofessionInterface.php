<?php

namespace App\Interfaces;

interface iprofessionInterface
{
    public function getAll($search,$tire_id);
    
    public function create($data);
    public function get($id);
    public function update($id, $data);
    public function delete($id);
    public function getDocuments($id);
    public function assigndocument($id, $document_id,$customertype_id);
    public function unassigndocument($id, $document_id,$customertype_id);
    public function createcondition($data);
    public function getcondition($id);
    public function updatecondition($id, $data);
    public function deletecondition($id);
}
