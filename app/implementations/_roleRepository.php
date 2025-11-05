<?php

namespace App\implementations;

use App\Interfaces\iroleInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class _roleRepository implements iroleInterface
{
    /**
     * Create a new class instance.
     */
    protected $role;
    protected $permission;
    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }
    public function getAll()
    {
        return $this->role->with('accounttype')->get();
    }
    public function getbyaccounttype($id)
    {
        return $this->role->where('accounttype_id', $id)->get();
    }
    public function get($id)
    {
        return $this->role->find($id);
    }
    public function create($data)
    {
        try {
            $check = $this->role->where('name', $data['name'])->first();
            if ($check) {
                return ['status' => 'error', 'message' => 'Role already exists'];
            }
            $this->role->create($data);
            return ['status' => 'success', 'message' => 'Role created successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function update($id, $data)
    {
        try {
            $check = $this->role->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ['status' => 'error', 'message' => 'Role already exists'];
            }
            $this->role->find($id)->update($data);
            return ['status' => 'success', 'message' => 'Role updated successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->role->find($id)->delete();
            return ['status' => 'success', 'message' => 'Role deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function getPermissions($id)
    {
        return $this->role->with('permissions')->where('id', $id)->first();
    }
    public function assignpermission($id, $permission_id)
    {
        try {
            $permission = $this->permission->find($permission_id);
            if(!$permission){
                return ['status' => 'error', 'message' => 'Permission not found'];
            }
            $this->role->find($id)->givePermissionTo($permission->name);
            return ['status' => 'success', 'message' => 'Permission assigned successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function removepermission($id, $permission_id)
    {
        try {
            $permission = $this->permission->find($permission_id);
            if(!$permission){
                return ['status' => 'error', 'message' => 'Permission not found'];
            }
            $this->role->find($id)->revokePermissionTo($permission->name);
            return ['status' => 'success', 'message' => 'Permission removed successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
