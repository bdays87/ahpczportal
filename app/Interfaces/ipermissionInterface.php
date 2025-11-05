<?php

namespace App\Interfaces;

interface ipermissionInterface
{
    public function getAll();
    public function getPermissions($id);
    
    public function create($data);
    public function get($id);
    public function update($id, $data);
    public function delete($id);
}
