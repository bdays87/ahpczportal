<?php

namespace App\implementations;

use App\Interfaces\ipenaltiesInterface;
use App\Models\Penalty;

class _penaltiesRepository implements ipenaltiesInterface
{
    /**
     * Create a new class instance.
     */
    protected $penalties;

    public function __construct(Penalty $penalties)
    {
        $this->penalties = $penalties;
    }

    public function getAll()
    {
        return $this->penalties->with('tire')->get();
    }
    public function get($id)
    {
        return $this->penalties->with('tire')->find($id);
    }

    public function create($data)
    {
        try {
            $penalty = $this->penalties->create($data);
            return ["status" => "success", "message" => "Penalty created successfully", "data" => $penalty];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $penalty = $this->penalties->find($id);
            if (!$penalty) {
                return ["status" => "error", "message" => "Penalty not found"];
            }
            $penalty->update($data);
            return ["status" => "success", "message" => "Penalty updated successfully", "data" => $penalty];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $penalty = $this->penalties->find($id);
            if (!$penalty) {
                return ["status" => "error", "message" => "Penalty not found"];
            }
            $penalty->delete();
            return ["status" => "success", "message" => "Penalty deleted successfully"];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
