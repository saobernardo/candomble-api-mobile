<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('create', [UserController::class, 'create']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('password-recovery-request', [UserController::class, 'passwordRecoveryRequest']);
    Route::get('validate-password-recovery/{token}/{encodedEmail}', [UserController::class, 'validatePasswordRecovery']);
    Route::put('password-change', [UserController::class, 'passwordChange']);
});
