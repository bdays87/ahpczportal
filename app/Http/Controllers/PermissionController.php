<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Interfaces\ipermissionInterface;

class PermissionController extends Controller
{
    protected $permissionrepository;

    public function __construct(ipermissionInterface $permissionrepository)
    {
        $this->permissionrepository = $permissionrepository;
    }

    public function index()
    {
        return $this->permissionrepository->getAll();
    }

    public function store(PermissionRequest $request)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->permissionrepository->create($validated);
    }

    public function show($id)
    {
        return $this->permissionrepository->get($id);
    }

    public function update(PermissionRequest $request, $id)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->permissionrepository->update($id, $validated);
    }

    public function destroy($id)
    {
        return $this->permissionrepository->delete($id);
    }
    public function getPermissions($id)
    {
        return $this->permissionrepository->getPermissions($id);
    }
}
