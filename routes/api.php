<?php

use App\Http\Controllers\AccounttypeController;
use App\Http\Controllers\Api\PractitionerIntegrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubmoduleController;
use App\Http\Controllers\SystemmoduleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Outbound-facing integration endpoints — other systems calling in
|--------------------------------------------------------------------------
| POST /api/integration/practitioners/resolve      (reg number -> profile, no code)
| POST /api/integration/practitioners/request-code  (legacy — unused by cpdapp now)
| POST /api/integration/practitioners/verify-code   (legacy — unused by cpdapp now)
|
| Currently used by the CPD Platform's "sign in with your MLCSCZ registration
| number" flow, which is reg-number-only now (see cpdapp's CouncilLoginService)
| — request-code/verify-code are kept only in case that decision is revisited.
| Signed with HMAC (see App\Http\Middleware\VerifyIntegrationSignature
| and config/integrations.php) — {partner} below must be a configured key.
*/
Route::middleware('verify.integration.signature:cpdapp')->prefix('integration')->group(function () {
    Route::post('practitioners/resolve', [PractitionerIntegrationController::class, 'resolve']);
    Route::post('practitioners/request-code', [PractitionerIntegrationController::class, 'requestCode']);
    Route::post('practitioners/verify-code', [PractitionerIntegrationController::class, 'verifyCode']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('tokenlogin', [AuthController::class, 'TokenLogin']);
Route::post('register', [AuthController::class, 'register']);

// Route::apiResource('accounttypes', AccounttypeController::class);
// Route::get('accounttypes/{id}/systemmodules', [AccounttypeController::class, 'getsystemmodules']);
// Route::apiResource('roles', RoleController::class);
// Route::get('roles/{id}/permissions', [RoleController::class, 'getPermissions']);
//  Route::post('roles/{id}/permissions', [RoleController::class, 'assignpermission']);
//  Route::delete('roles/{id}/permissions', [RoleController::class, 'removepermission']);
// Route::apiResource('users', UserController::class);
// Route::get('users/{id}/roles', [UserController::class, 'getRoles']);
// Route::get('users/{id}/permissions', [UserController::class, 'getPermissions']);
// Route::post('users/{id}/roles', [UserController::class, 'assignrole']);
// /Route::post('users/{id}/permissions', [UserController::class, 'assignpermission']);
// /Route::delete('users/{id}/{roleid}', [UserController::class, 'removerole']);
// Route::delete('users/{id}/{permissionid}', [UserController::class, 'removepermission']);

Route::apiResource('permissions', PermissionController::class);
Route::get('permissions/{id}/submodule', [PermissionController::class, 'getPermissions']);
//   Route::apiResource('systemmodules', SystemmoduleController::class);
// Route::get('systemmodules/{id}/submodules', [SystemmoduleController::class, 'getSubmodules']);
// Route::apiResource('submodules', SubmoduleController::class);
// Route::get('submodules/{id}/permissions', [SubmoduleController::class, 'getpermissions']);

Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('me', [AuthController::class, 'me']);
