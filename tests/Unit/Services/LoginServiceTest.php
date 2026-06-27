<?php

namespace Tests\Unit\Services;

use App\Exceptions\InvalidParamException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Services\JWTService;
use App\Services\PasswordService;
use App\Services\User\GetUserService;
use App\Services\User\LoginService;
use Mockery;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testLoginSuccessfully(): void
    {
        $passwordService = Mockery::mock(PasswordService::class);
        $jwtService = Mockery::mock(JWTService::class);
        $getUserService = Mockery::mock(GetUserService::class);

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->password = '$2y$10$hashedpassword';

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        $passwordService->shouldReceive('check')
            ->once()
            ->andReturn(true);

        $jwtService->shouldReceive('createJWTToken')
            ->once()
            ->with([
                'email' => 'test@example.com',
                'accountType' => 'user',
            ])
            ->andReturn('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.test.token');

        $service = new LoginService($passwordService, $jwtService, $getUserService);

        $result = $service->login('test@example.com', 'Password123!');

        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.test.token', $result);
    }

    public function testLoginThrowsExceptionWhenUserNotFound(): void
    {
        $passwordService = Mockery::mock(PasswordService::class);
        $jwtService = Mockery::mock(JWTService::class);
        $getUserService = Mockery::mock(GetUserService::class);

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(null);

        $service = new LoginService($passwordService, $jwtService, $getUserService);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('user not found');

        $service->login('nonexistent@example.com', 'Password123!');
    }

    public function testLoginThrowsExceptionWhenPasswordIsIncorrect(): void
    {
        $passwordService = Mockery::mock(PasswordService::class);
        $jwtService = Mockery::mock(JWTService::class);
        $getUserService = Mockery::mock(GetUserService::class);

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->password = '$2y$10$hashedpassword';

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        $passwordService->shouldReceive('check')
            ->once()
            ->andReturn(false);

        $service = new LoginService($passwordService, $jwtService, $getUserService);

        $this->expectException(InvalidParamException::class);
        $this->expectExceptionMessage('password incorrect');

        $service->login('test@example.com', 'WrongPassword');
    }
}
