<?php

use App\Http\Controllers\AccounttypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubmoduleController;
use App\Http\Controllers\SystemmoduleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('tokenlogin', [AuthController::class, 'TokenLogin']);
Route::post('register', [AuthController::class, 'register']);

  
       // Route::apiResource('accounttypes', AccounttypeController::class);
       // Route::get('accounttypes/{id}/systemmodules', [AccounttypeController::class, 'getsystemmodules']);
        //Route::apiResource('roles', RoleController::class);
       // Route::get('roles/{id}/permissions', [RoleController::class, 'getPermissions']);
      //  Route::post('roles/{id}/permissions', [RoleController::class, 'assignpermission']);
      //  Route::delete('roles/{id}/permissions', [RoleController::class, 'removepermission']);
       // Route::apiResource('users', UserController::class);
       // Route::get('users/{id}/roles', [UserController::class, 'getRoles']);
       // Route::get('users/{id}/permissions', [UserController::class, 'getPermissions']);
       // Route::post('users/{id}/roles', [UserController::class, 'assignrole']);
        ///Route::post('users/{id}/permissions', [UserController::class, 'assignpermission']);
        ///Route::delete('users/{id}/{roleid}', [UserController::class, 'removerole']);
       // Route::delete('users/{id}/{permissionid}', [UserController::class, 'removepermission']);
        
        
        Route::apiResource('permissions', PermissionController::class);
        Route::get('permissions/{id}/submodule', [PermissionController::class, 'getPermissions']);
     //   Route::apiResource('systemmodules', SystemmoduleController::class);
        //Route::get('systemmodules/{id}/submodules', [SystemmoduleController::class, 'getSubmodules']);
        //Route::apiResource('submodules', SubmoduleController::class);
       // Route::get('submodules/{id}/permissions', [SubmoduleController::class, 'getpermissions']);
        
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
  
        