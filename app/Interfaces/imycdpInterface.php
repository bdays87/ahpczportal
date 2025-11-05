<?php

namespace App\Interfaces;

interface imycdpInterface
{
    public function create($data);
    public function update($id,$data);
    public function delete($id);
    public function get($id);
    public function savesassessment($data);
    public function saveattachment($data);
    public function deleteattachment($id);
    public function submitforassessment($id);
    public function getbycustomerprofession($id,$year);
    public function getcdps($year,$status);
    public function assignpoints($id,$points);
}
