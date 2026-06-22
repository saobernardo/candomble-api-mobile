<?php

use App\Http\Controllers\UserController;
use App\Http\Middlewares\AuthUserMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => AuthUserMiddleware::class,
], function () {
    Route::post('register', [UserController::class, 'register'])->withoutMiddleware(AuthUserMiddleware::class);
});
