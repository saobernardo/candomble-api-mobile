<?php

namespace Tests\Unit\Services;

use App\DTO\User\CreateUserDTO;
use App\Exceptions\ConflictException;
use App\Exceptions\InvalidParamException;
use App\Models\User;
use App\Services\PasswordService;
use App\Services\User\CreateUserService;
use App\Services\User\GetUserService;
use Mockery;
use Tests\TestCase;

class CreateUserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateServiceCanBeInstantiated(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);

        $service = new CreateUserService($getUserService, $passwordService);

        $this->assertInstanceOf(CreateUserService::class, $service);
    }

    public function testValidateUserThrowsExceptionWhenEmailIsEmpty(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);

        $service = new CreateUserService($getUserService, $passwordService);

        $dto = CreateUserDTO::fromArray([
            'email' => '',
            'fullName' => 'Test User',
            'cpf' => '12345678901',
            'rg' => null,
            'password' => 'Password123!',
        ]);

        $this->expectException(InvalidParamException::class);
        $this->expectExceptionMessage('user email not provided');

        $service->validateUser($dto);
    }

    public function testValidateUserThrowsExceptionWhenCpfIsInvalid(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);

        $service = new CreateUserService($getUserService, $passwordService);

        $dto = CreateUserDTO::fromArray([
            'email' => 'test@example.com',
            'fullName' => 'Test User',
            'cpf' => '00000000000',
            'password' => 'Password123!',
        ]);

        $this->expectException(InvalidParamException::class);
        $this->expectExceptionMessage('invalid CPF');

        $service->validateUser($dto);
    }

    public function testValidateUserThrowsExceptionWhenUserExists(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);

        $existingUser = new User();
        $existingUser->id = 1;
        $existingUser->email = 'test@example.com';
        $existingUser->activated = true;

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($existingUser);

        $service = new CreateUserService($getUserService, $passwordService);

        $dto = CreateUserDTO::fromArray([
            'email' => 'test@example.com',
            'fullName' => 'Test User',
            'cpf' => '12345678909',
            'password' => 'Password123!',
        ]);

        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('user already exists');

        $service->validateUser($dto);
    }

    public function testCheckClientExistsReturnsTrueWhenUserIsActivated(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);

        $existingUser = new User();
        $existingUser->activated = true;

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($existingUser);

        $service = new CreateUserService($getUserService, $passwordService);

        $dto = CreateUserDTO::fromArray([
            'email' => 'test@example.com',
            'fullName' => 'Test User',
            'cpf' => '12345678909',
            'password' => 'Password123!',
        ]);

        $result = $service->checkClientExists($dto);

        $this->assertTrue($result);
    }

    public function testCheckClientExistsReturnsFalseWhenUserDoesNotExist(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn(null);

        $service = new CreateUserService($getUserService, $passwordService);

        $dto = CreateUserDTO::fromArray([
            'email' => 'test@example.com',
            'fullName' => 'Test User',
            'cpf' => '12345678909',
            'password' => 'Password123!',
        ]);

        $result = $service->checkClientExists($dto);

        $this->assertFalse($result);
    }
}
