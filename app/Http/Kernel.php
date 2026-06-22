<?php

namespace App\Http;

use App\Http\Middlewares\AuthUserMiddleware;
use Symfony\Component\HttpKernel\HttpKernel;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        'auth' => AuthUserMiddleware::class,
    ];
}
