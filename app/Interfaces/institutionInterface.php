<?php

namespace App\Interfaces;

interface institutionInterface
{
    public function getAll($search=null);
    public function getAllOptions();
    public function get($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
