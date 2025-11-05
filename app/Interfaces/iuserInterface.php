<?php

namespace App\Interfaces;

interface iuserInterface
{
    public function getAll($search,$accounttypefilter);
    public function create($data);
    public function get($id);
    public function get_by_uuid($uuid);
    public function update($id, $data);
    public function delete($id);
    public function getRoles($id);
    public function assignrole($id, $roleid);
    public function removerole($id, $roleid);
    public function getPermissions($id);
    public function assignpermission($id, $permissionid);
    public function removepermission($id, $permissionid);
}
