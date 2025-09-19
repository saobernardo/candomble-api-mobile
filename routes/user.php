<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Middlewares\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('ping', [Controller::class, 'ping']);

Route::group([
    'prefix' => 'user',
    'middleware' => AuthMiddleware::class,
], function () {
    Route::post('register', [UserController::class, 'register']);
});
