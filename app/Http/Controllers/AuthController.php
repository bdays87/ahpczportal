<?php

namespace App\Http\Controllers;

use App\Http\Requests\UseraccountRequest;
use App\Interfaces\iuserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userrepository;
    public function __construct(iuserInterface $userrepository)
    {
        $this->userrepository = $userrepository;
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $check = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        if ($check) {
          
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ], 401);
    }
    public function TokenLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $check = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        if ($check) {
            $token = auth()->user->createToken('token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ], 401);
    }
    public function register(UseraccountRequest $request)
    {
        $data = $request->validated();
        if(!$data){
            return response()->json([
                "status"=>"error",
                "message"=>"No data provided"
            ],422);
        }
        $response = $this->userrepository->create($data);
        if($response['status']=='error'){
            return response()->json([
                'status' => 'error',
                'message' => $response['message'],
            ],422);
        }else{
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
        ]);
    }
    }
  
    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully',
        ]);
    }
    public function refresh(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User refreshed successfully',
        ]);
    }
    public function me(Request $request)
    {
        $permissions = $this->userrepository->getPermissions($request->user()->id);
        $roles = $this->userrepository->getRoles($request->user()->id);
        $request->user()->permissions = $permissions;
        $request->user()->roles = $roles;
        return $request->user();
    }
}
