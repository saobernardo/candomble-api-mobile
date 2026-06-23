<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('create', [UserController::class, 'create']);
    Route::post('login', [UserController::class, 'login']);
});
