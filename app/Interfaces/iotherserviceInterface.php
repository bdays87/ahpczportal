<?php

namespace App\Interfaces;

interface iotherserviceInterface
{
    public function getAll($search);
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);


    public function getdocuments($id);
    public function createdocument($id, $document_id);
    public function deletedocument($id,$document_id);
}
