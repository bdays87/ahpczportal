<?php

namespace App\Interfaces;

interface iqualificationInterface
{
    public function createQualification(array $data);
    public function updateQualification(array $data,$id);
    public function deleteQualification($id);
    public function getQualificationById(int $id);
    public function  getQualificationByProfessionId(int $profession_id);
    public function getAllQualifications($search = null, $profession_id);
    public function searchQualifications($search = null);
}
