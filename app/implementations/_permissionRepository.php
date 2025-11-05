<?php

namespace App\implementations;

use App\Interfaces\ipermissionInterface;
use App\Models\Submodule;
use Spatie\Permission\Models\Permission;

class _permissionRepository implements ipermissionInterface
{
    /**
     * Create a new class instance.
     */
    protected $permission;
    protected $submodule;
    public function __construct(Permission $permission, Submodule $submodule)
    {
        $this->permission = $permission;
        $this->submodule = $submodule;
    }
    public function getAll()
    {
        return $this->permission->all();
    }
    public function create($data)
    {
        try {
            $check = $this->permission->where('name', $data['name'])->first();
            if ($check) {
                return ["status" => "error", "message" => "Permission already exists"];
            }
            $this->permission->create($data);
            return ["status" => "success", "message" => "Permission created successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function get($id)
    {
        return $this->permission->find($id);
    }
    public function update($id, $data)
    {
        try {
            $check = $this->permission->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status" => "error", "message" => "Permission already exists"];
            }
            $this->permission->find($id)->update($data);
            return ["status" => "success", "message" => "Permission updated successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->permission->find($id)->delete();
            return ["status" => "success", "message" => "Permission deleted successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function getPermissions($id)
    {
       
         $submodule = $this->submodule->where('id', $id)->with('systemmodule')->first();
      
         $checksubmodule = $this->permission->where('name', $submodule->default_permission)->first();
      
         $checksystemmodule = $this->permission->where('name', $submodule->systemmodule->default_permission)->first();
    
         if (!$checksubmodule) {
            $this->permission->create(['name' => $submodule->default_permission,'submodule_id'=>$submodule->id]);
         }
         if (!$checksystemmodule) {
            $this->permission->create(['name' => $submodule->systemmodule->default_permission,'submodule_id'=>$submodule->id]);
         }
         return $this->permission->where('submodule_id', $id)->get();
    }
}
