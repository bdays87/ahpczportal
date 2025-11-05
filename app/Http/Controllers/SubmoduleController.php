<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmoduleRequest;
use App\Interfaces\isubmoduleInterface;
use Illuminate\Http\Request;

class SubmoduleController extends Controller
{
    protected $submodulerpository;

    public function __construct(isubmoduleInterface $submodulerpository)
    {
        $this->submodulerpository = $submodulerpository;
    }

    public function index()
    {
        return $this->submodulerpository->getAll();
    }

    public function store(SubmoduleRequest $request)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->submodulerpository->create($validated);
    }

    public function show($id)
    {
        return $this->submodulerpository->get($id);
    }
    public function getpermissions($id)
    {
        return $this->submodulerpository->getpermissions($id);
    }
    public function update(SubmoduleRequest $request, $id)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->submodulerpository->update($id, $validated);
    }

    public function destroy($id)
    {
        return $this->submodulerpository->delete($id);
    }
   
}
