<?php

namespace App\Interfaces;

interface icustomerInterface
{
    public function getAll($search, $filters = []);
    public function getallsearch($search, $filters = []);
    public function get($id);
    public function create($data);
    public function register($data);
    public function update($id, $data);
    public function updateprofile($id, $data);
    public function delete($id);
    public function getcustomerprofile($uuid);
    public function importcustomersexcel($path);
}
