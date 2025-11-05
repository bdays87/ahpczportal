<?php

namespace App\implementations;

use App\Interfaces\isubmoduleInterface;
use App\Models\Submodule;

class _submoduleRepository implements isubmoduleInterface
{
    /**
     * Create a new class instance.
     */
    protected $submodule;
    public function __construct(Submodule $submodule)
    {
        $this->submodule = $submodule;
    }

    public function getAll($id)
    {
        
            return $this->submodule->where('systemmodule_id', $id)->get();
      
    }
    public function create($data)
    {
        try {
            $check = $this->submodule->where('name', $data['name'])->first();
            if ($check) {
                return ["status" => "error", "message" => "Submodule already exists"];
            }
            $this->submodule->create($data);
            return ["status" => "success", "message" => "Submodule created successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function get($id)
    {
        return $this->submodule->with('permissions')->where('id', $id)->first();
    }
    public function getpermissions($id)
    {
        return $this->submodule->with('permissions')->where('id', $id)->first();
    }
    public function update($id, $data)
    {
        try {
            $check = $this->submodule->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status" => "error", "message" => "Submodule already exists"];
            }
            $this->submodule->find($id)->update($data);
            return ["status" => "success", "message" => "Submodule updated successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->submodule->find($id)->delete();
            return ["status" => "success", "message" => "Submodule deleted successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
