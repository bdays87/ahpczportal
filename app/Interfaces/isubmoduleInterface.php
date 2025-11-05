<?php

namespace App\Interfaces;

interface isubmoduleInterface
{
    public function getAll($id);
    public function create($data);
    public function get($id);
    public function getpermissions($id);
    public function update($id, $data);
    public function delete($id);
}
