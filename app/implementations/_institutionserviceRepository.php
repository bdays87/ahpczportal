<?php

namespace App\implementations;

use App\Interfaces\iinstitutionserviceInterface;
use App\Models\InstitutionService;

class _institutionserviceRepository implements iinstitutionserviceInterface
{
    protected $model;

    public function __construct(InstitutionService $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('institution')->orderBy('name')->get();
    }

    public function getActive()
    {
        return $this->model->with('institution')->where('status', 'active')->orderBy('name')->get();
    }

    public function getByInstitution($institution_id)
    {
        return $this->model->where('institution_id', $institution_id)->orderBy('name')->get();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function create($data)
    {
        try {
            $this->model->create($data);
            return ['status' => 'success', 'message' => 'Institution service created successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $this->model->where('id', $id)->update($data);
            return ['status' => 'success', 'message' => 'Institution service updated successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->model->where('id', $id)->delete();
            return ['status' => 'success', 'message' => 'Institution service deleted successfully'];
        } catch (\Throwable $th) {
            return ['status' => 'error', 'message' => $th->getMessage()];
        }
    }
}
