<?php

namespace App\Services\User;

use App\Exceptions\InvalidParamException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Services\JWTService;
use App\Services\PasswordService;

/**
 * Service responsible for authenticating users.
 *
 * Authentication flow:
 * - Retrieve user by email
 * - Validate password
 * - Generate JWT token
 */
class LoginService
{
    /**
     * LoginService constructor
     *
     * @param  PasswordService  $passwordService
     * @param  JWTService  $jwtService
     * @param  GetUserService  $getUserService
     */
    public function __construct(
        protected PasswordService $passwordService,
        protected JWTService $jwtService,
        protected GetUserService $getUserService,
    ) {}

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param  string  $email  User email.
     * @param  string  $password  Plain-text password.
     *
     * @return string Generated JWT token.
     */
    public function login(string $email, string $password): string
    {
        $user = $this->getUser($email);

        $this->passwordCheck($password, $user->password);

        $params = [
            'email' => $email,
            'accountType' => 'user',
        ];

        return $this->jwtService->createJWTToken($params);
    }

    /**
     * Retrieve a user by email.
     *
     * @param  string  $email
     *
     * @return User
     *
     * @throws NotFoundException if user is not found
     */
    private function getUser(string $email): User
    {
        $user = $this->getUserService->getByEmail($email);

        if (empty($user)) {
            throw new NotFoundException('C046', 'user not found', 'usuário não encontrado');
        }

        return $user;
    }

    /**
     * Validate the provided password against the stored hash.
     *
     * @param  string  $password  Plain password.
     * @param  string  $userPassword  Stored hashed password.
     *
     * @return void
     *
     * @throws InvalidParamException if given password is not correct
     */
    private function passwordCheck(string $password, string $userPassword): void
    {
        $password = $this->passwordService->check($password, $userPassword);

        if (!$password) {
            throw new InvalidParamException('C008', 'password incorrect', 'usuário ou senha incorreto(s)');
        }
    }
}
