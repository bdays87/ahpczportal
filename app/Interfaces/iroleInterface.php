<?php

namespace App\Interfaces;

interface iroleInterface
{
    public function getAll();
    public function get($id);
    public function getbyaccounttype($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getPermissions($id);
    public function assignpermission($id, $permission_id);
    public function removepermission($id, $permission_id);
}
