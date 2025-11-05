<?php

namespace App\implementations;

use App\Interfaces\isystemmoduleInterface;
use App\Models\Systemmodule;

class _systemmoduleRepository implements isystemmoduleInterface
{
    /**
     * Create a new class instance.
     */
    protected $systemmodule;
    public function __construct(Systemmodule $systemmodule)
    {
        $this->systemmodule = $systemmodule;
    }

    public function getAll()
    {
        
        return $this->systemmodule->with('accounttype')->get();
    }
    public function getmenu()
    {
        return $this->systemmodule->with('submodules.permissions')->get();
    }
    public function getfullmenubyaccounttype($id)
    {
        return $this->systemmodule->with('submodules.permissions')->where('accounttype_id', $id)->get();
    }
    public function create($data)
    {
        try {
        $check = $this->systemmodule->where('name', $data['name'])->first();
        if ($check) {
            return ["status" => "error", "message" => "System Module already exists"];
        }

        $this->systemmodule->create($data);
        return ["status" => "success", "message" => "System Module created successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function getSubmodules($id)
    {
        return $this->systemmodule->with('submodules')->find($id);
    }
    public function get($id)
    {
        return $this->systemmodule->find($id);
    }
    public function update($id, $data)
    {
        try {
            $check = $this->systemmodule->where('name', $data['name'])->where('id', '!=', $id)->first();
            if ($check) {
                return ["status" => "error", "message" => "System Module already exists"];
            }
            $this->systemmodule->find($id)->update($data);
            return ["status" => "success", "message" => "System Module updated successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    public function delete($id)
    {
        try {
            $this->systemmodule->find($id)->delete();
            return ["status" => "success", "message" => "System Module deleted successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
