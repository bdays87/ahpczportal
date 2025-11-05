<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccounttypeRequest;
use App\interfaces\iaccounttypeInterface;
use Illuminate\Http\Request;

class AccounttypeController extends Controller
{
    protected $accounttyperepository;

    public function __construct(iaccounttypeInterface $accounttyperepository)
    {
        $this->accounttyperepository = $accounttyperepository;
    }

    public function index()
    {
        return $this->accounttyperepository->getAll();
    }

    public function store(AccounttypeRequest $request)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->accounttyperepository->create($validated);
    }

    public function show($id)
    {
        return $this->accounttyperepository->get($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validated();
        if(!$validated){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validated->errors()
            ], 422);
        }
        return $this->accounttyperepository->update($id, $validated);
    }

    public function destroy($id)
    {
        return $this->accounttyperepository->delete($id);
    }
    public function getsystemmodules($id)
    {
        return $this->accounttyperepository->getsystemmodules($id);
    }
}
