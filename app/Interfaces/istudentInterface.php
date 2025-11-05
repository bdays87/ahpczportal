<?php

namespace App\Interfaces;

interface istudentInterface
{
   
    public function createqualification($data);
    public function getqualification($id);
    public function updatequalification($id, $data);
    public function deletequalification($id);
    public function createplacement($data);
    public function getplacement($id);
    public function updateplacement($id, $data);
    public function deleteplacement($id);
}
