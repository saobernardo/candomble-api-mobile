<?php

namespace App\Http\Controllers;

use App\Services\User\CreateUserService;

class UserController
{
    public function __construct(
        protected CreateUserService $createUserService
    ) {}

    public function register() {}
}
