<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Middlewares\AuthUserMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('ping', [Controller::class, 'ping']);

Route::group([
    'middleware' => AuthUserMiddleware::class,
], function () {
    Route::post('register', [UserController::class, 'register'])->withoutMiddleware(AuthUserMiddleware::class);
});
