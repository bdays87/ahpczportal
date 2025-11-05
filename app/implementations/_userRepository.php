<?php

namespace App\implementations;

use App\Interfaces\iroleInterface;
use App\Interfaces\iuserInterface;
use App\Models\User;
use App\Notifications\AccountcreatedNotification;
use App\Notifications\Newaccountcreated;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class _userRepository implements iuserInterface
{
    /**
     * Create a new class instance.
     */
    protected $user;
    protected $permission;
    protected $role;
    protected $irolerepo;
    public function __construct(User $user, Permission $permission, Role $role, iroleInterface $irolerepo)
    {
        $this->user = $user;
        $this->permission = $permission;
        $this->role = $role;
        $this->irolerepo = $irolerepo;
    }
    public function getAll($search,$accounttypefilter)
    {
        return $this->user->with('accounttype')->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('surname', 'like', "%{$search}%");
        })->when($accounttypefilter, function ($query)use($accounttypefilter){
            $query->where('accounttype_id', $accounttypefilter);
        })->paginate(10);
    }
    public function create($data)
    {
        try {
            $check = $this->user->where('email', $data['email'])->first();
            if ($check) {
                return ["status" => "error", "message" => "Email already taken"];
            }
            $password = Str::random(8);
            $data['password'] = $password;
            $data['uuid'] = Str::uuid();
            $this->user->create($data);
            $user = $this->user->where('email', $data['email'])->first();
            $user->notify(new AccountcreatedNotification($data['name'], $data['email'], $password));
            return ["status" => "success", "message" => "User created successfully","data"=>$user];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function register($data){
        try {
            $check = $this->user->where('email', $data['email'])->first();
            if ($check) {
                return ["status" => "error", "message" => "Email already taken"];
            }
            $data['uuid'] = Str::uuid();
            $this->user->create($data);
            $user = $this->user->where('email', $data['email'])->first();
           $roles = $this->irolerepo->getbyaccounttype($data['accounttype_id']);
           foreach ($roles as $role) {
            $this->assignrole($user->id, $role->id);
           }
            
            $user->notify(new Newaccountcreated($data['name'], $data['email']));
            return ["status" => "success", "message" => "User created successfully","data"=>$user];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function get($id)
    {
        return $this->user->with('roles')->with('permissions')->find($id);
    }
    public function get_by_uuid($uuid)
    {
        return $this->user->with('roles')->with('permissions')->where('uuid', $uuid)->first();
    }
    public function update($id, $data)
    {
        try {
            $check = $this->user->where('email', $data['email'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status" => "error", "message" => "Email already taken"];
            }
            $user=$this->user->where('id', $id)->first();
            $user->uuid = $user->uuid=="" ? Str::uuid():$user->uuid;
            $user->update($data);
            return ["status" => "success", "message" => "User updated successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->user->find($id)->delete();
            return ["status" => "success", "message" => "User deleted successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function getRoles($id)
    {
        $directpermissions = $this->user->where('id', $id)->first()->getPermissionsViaRoles()->pluck('id')->toArray();
        $userpermissions = $this->user->where('id', $id)->first()->getDirectPermissions()->pluck('id')->toArray();
        $permissions = array_merge($directpermissions, $userpermissions);
        $roles = $this->user->with('roles')->where('id', $id)->first()->roles->pluck('name')->toArray();
        
        return [
            "permissions"=>$permissions,
            "roles"=>$roles
        ];
    }
    public function assignrole($id, $roleid)
    {
        try {
            $role = $this->role->find($roleid);
            if(!$role){
                return ["status" => "error", "message" => "Role not found"];
            }
            $this->user->where('id', $id)->first()->assignRole($role->name);
            return ["status" => "success", "message" => "Role assigned successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function removerole($id, $roleid)
    {
        try {
            $role = $this->role->find($roleid);
            if(!$role){
                return ["status" => "error", "message" => "Role not found"];
            }
            $this->user->find($id)->removeRole($role->name);
            return ["status" => "success", "message" => "Role removed successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function getPermissions($id)
    {
        return $this->user->with('permissions')->where('id', $id)->first();
    }
    public function assignpermission($id, $permissionid)
    {
        try {
            $permission = $this->permission->find($permissionid);
            if(!$permission){
                return ["status" => "error", "message" => "Permission not found"];
            }
            $user = $this->user->find($id);
            $permissions = $user->getPermissionsViaRoles()->pluck('id')->toArray();
            if(in_array($permission->id, $permissions)){
                return ["status" => "error", "message" => "Permission already assigned globally to user role"];
            }
            $user->givePermissionTo($permission->name);
            return ["status" => "success", "message" => "Permission assigned successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function removepermission($id, $permissionid)
    {
        try {
            $permission = $this->permission->find($permissionid);
            if(!$permission){
                return ["status" => "error", "message" => "Permission not found"];
            }
            if(!$this->user->where('id', $id)->first()->hasDirectPermission($permission->name)){
                return ["status" => "error", "message" => "Permission not assigned directly to user"];
            }
            $this->user->where('id', $id)->first()->revokePermissionTo($permission->name);
            return ["status" => "success", "message" => "Permission removed successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }   
    }
}
