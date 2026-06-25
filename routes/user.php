<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('create', [UserController::class, 'create']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('password-recovery-request', [UserController::class, 'passwordRecoveryRequest']);
    // Route::get('validatePasswordRecovery/{token}/{encodedEmail}', [AuthController::class, 'validatePasswordRecovery']);
    // Route::put('passwordChange', [AuthController::class, 'passwordChange']);
});
