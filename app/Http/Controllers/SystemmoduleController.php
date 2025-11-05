<?php

namespace App\Http\Controllers;

use App\Http\Requests\SysytemModuleRequest;
use App\Interfaces\isystemmoduleInterface;
use Illuminate\Http\Request;

class SystemmoduleController extends Controller
{
    protected $systemmodulerpository;

    public function __construct(isystemmoduleInterface $systemmodulerpository)
    {
        $this->systemmodulerpository = $systemmodulerpository;
    }
    public function index(){
        return $this->systemmodulerpository->getAll();
    }
    public function store(SysytemModuleRequest $request){    
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->systemmodulerpository->create($validated);
    }
    public function show($id){
        return $this->systemmodulerpository->get($id);
    }
    public function update(SysytemModuleRequest $request, $id){
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->systemmodulerpository->update($id, $validated);
    }
    public function destroy($id){
        return $this->systemmodulerpository->delete($id);
    }
    public function getSubmodules($id){
        return $this->systemmodulerpository->getSubmodules($id);
    }
   
}
