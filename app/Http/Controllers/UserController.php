<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAssignRequest;
use App\Interfaces\iuserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $userrepository;

    public function __construct(iuserInterface $userrepository)
    {
        $this->userrepository = $userrepository;
    }

    public function index(Request $request)
    {
        return $this->userrepository->getAll($request->search);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $this->userrepository->create($request->all());
    }
    public function show($id)
    {
        return $this->userrepository->get($id);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $this->userrepository->update($id, $request->all());
    }
    public function destroy($id)
    {
        return $this->userrepository->delete($id);
    }
    public function getRoles($id)
    {
        return $this->userrepository->getRoles($id);
    }
    public function assignrole($id, UserAssignRequest $request)
    {
        $data = $request->validated();
        if(!$data){
            return response()->json([
                "status"=>"error",
                "message"=>"No data provided"
            ],422);
        }
        return $this->userrepository->assignrole($id, $data['payload']);
    }
    public function removerole($id, $roleid)
    {
     
        return $this->userrepository->removerole($id, $roleid);
    }
    public function getPermissions($id)
    {
        return $this->userrepository->getPermissions($id);
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
        return $this->userrepository->assignpermission($id, $data['payload']);
    }
    public function removepermission($id, $permissionid)
    {
        return $this->userrepository->removepermission($id, $permissionid);
    }
}
