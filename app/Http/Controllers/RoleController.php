<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Requests\UserAssignRequest;
use App\Interfaces\iroleInterface;

class RoleController extends Controller
{
    protected $rolerepository;

    public function __construct(iroleInterface $rolerepository)
    {
        $this->rolerepository = $rolerepository;
    }

    public function index()
    {
        return $this->rolerepository->getAll();
    }

    public function store(RoleRequest $request)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->rolerepository->create($validated);
    }

    public function show($id)
    {
        return $this->rolerepository->get($id);
    }

    public function update(RoleRequest $request, $id)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->rolerepository->update($id, $request->all());
    }

    public function destroy($id)
    {
        return $this->rolerepository->delete($id);
    }
    public function getPermissions($id)
    {
        return $this->rolerepository->getPermissions($id);
    }
    public function assignpermission($id, UserAssignRequest $request)
    {
        $data = $request->validated();
        if(!$data){
            return response()->json([
                "status"=>"error",
                "message"=>"No data provided"
            ],422);
        }
        return $this->rolerepository->assignpermission($id, $data['payload']);
    }
    public function removepermission($id, UserAssignRequest $request)
    {
        $data = $request->validated();
        if(!$data){
            return response()->json([
                "status"=>"error",
                "message"=>"No data provided"
            ],422);
        }
        return $this->rolerepository->removepermission($id, $data['payload']);
    }
}
