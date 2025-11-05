<?php

namespace App\Interfaces;

interface icustomerprofessionInterface
{
public function getAll($status = "PENDING",$year = null);
public function getbyuuid($uuid);
public function getcustomerstudent($uuid);
public function getapplicationbyuuid($uuid);
public function create($data);
public function update($id,$data);
public function delete($id);
public function generateregistrationinvoice($id); 
public function generatepractitionerinvoice($id); 
public function renew($id,$data);

public function uploadDocument(array $data);
public function removedocument($document_id,$customerprofession_id);
public function verifydocument($document_id,$customerprofession_id); 



public function addqualification($data);
public function getqualification($id);
public function removequalification($id);
public function updatequalification($id,$data);

public function addcomment($data);
public function getcomment($id);
public function removecomment($id);
public function updatecomment($id,$data);

public function generateregistrationcertificate($id);
public function generatestudentcertificate($id);
public function generatepractisingcertificate($id);

public function uploadrenewaldocuments($data);
public function removerenewaldocument($document_id,$customerapplication_id);



}
