<?php

namespace Tests\Unit\Controllers;

use App\DTO\User\CreateUserDTO;
use App\Http\Controllers\UserController;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\PasswordChangeRequest;
use App\Http\Requests\User\PasswordRecoveryRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\ValidatePasswordRecoveryResource;
use App\Models\User;
use App\Models\UserPasswordResetToken;
use App\Services\User\CreateUserService;
use App\Services\User\GetPasswordResetRequestService;
use App\Services\User\LoginService;
use App\Services\User\PasswordChangeService;
use App\Services\User\PasswordRecoveryService;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateUserSuccessfully(): void
    {
        $createUserService = Mockery::mock(CreateUserService::class);
        $loginService = Mockery::mock(LoginService::class);
        $passwordRecoveryService = Mockery::mock(PasswordRecoveryService::class);
        $getPasswordResetRequestService = Mockery::mock(GetPasswordResetRequestService::class);
        $passwordChangeService = Mockery::mock(PasswordChangeService::class);

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->full_name = 'Test User';
        $user->cpf = '12345678901';

        $createUserService->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $request = Mockery::mock(CreateUserRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andReturn([
                'email' => 'test@example.com',
                'fullName' => 'Test User',
                'cpf' => '12345678901',
                'password' => 'Password123!',
            ]);

        $controller = new UserController(
            $createUserService,
            $loginService,
            $passwordRecoveryService,
            $getPasswordResetRequestService,
            $passwordChangeService
        );

        $response = $controller->create($request);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('S001', $data['code']);
        $this->assertEquals('success', $data['message']);
    }

    public function testLoginSuccessfully(): void
    {
        $createUserService = Mockery::mock(CreateUserService::class);
        $loginService = Mockery::mock(LoginService::class);
        $passwordRecoveryService = Mockery::mock(PasswordRecoveryService::class);
        $getPasswordResetRequestService = Mockery::mock(GetPasswordResetRequestService::class);
        $passwordChangeService = Mockery::mock(PasswordChangeService::class);

        $jwtToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.test.token';

        $loginService->shouldReceive('login')
            ->once()
            ->with('test@example.com', 'Password123!')
            ->andReturn($jwtToken);

        $request = Mockery::mock(LoginRequest::class);
        $request->shouldReceive('validated')
            ->with('email')
            ->andReturn('test@example.com');
        $request->shouldReceive('validated')
            ->with('password')
            ->andReturn('Password123!');

        $controller = new UserController(
            $createUserService,
            $loginService,
            $passwordRecoveryService,
            $getPasswordResetRequestService,
            $passwordChangeService
        );

        $response = $controller->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('S001', $data['code']);
        $this->assertEquals($jwtToken, $data['data']['jwtToken']);
    }

    public function testPasswordRecoveryRequestSuccessfully(): void
    {
        $createUserService = Mockery::mock(CreateUserService::class);
        $loginService = Mockery::mock(LoginService::class);
        $passwordRecoveryService = Mockery::mock(PasswordRecoveryService::class);
        $getPasswordResetRequestService = Mockery::mock(GetPasswordResetRequestService::class);
        $passwordChangeService = Mockery::mock(PasswordChangeService::class);

        $passwordRecoveryService->shouldReceive('requestPasswordChange')
            ->once()
            ->with('test@example.com')
            ->andReturnNull();

        $request = Mockery::mock(PasswordRecoveryRequest::class);
        $request->shouldReceive('validated')
            ->with('email')
            ->andReturn('test@example.com');

        $controller = new UserController(
            $createUserService,
            $loginService,
            $passwordRecoveryService,
            $getPasswordResetRequestService,
            $passwordChangeService
        );

        $response = $controller->passwordRecoveryRequest($request);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('S001', $data['code']);
        $this->assertNull($data['data']);
    }

    public function testValidatePasswordRecoverySuccessfully(): void
    {
        $createUserService = Mockery::mock(CreateUserService::class);
        $loginService = Mockery::mock(LoginService::class);
        $passwordRecoveryService = Mockery::mock(PasswordRecoveryService::class);
        $getPasswordResetRequestService = Mockery::mock(GetPasswordResetRequestService::class);
        $passwordChangeService = Mockery::mock(PasswordChangeService::class);

        $resetRequest = new UserPasswordResetToken();
        $resetRequest->id = 1;
        $resetRequest->email = 'test@example.com';
        $resetRequest->token = 'reset-token-12345';
        $resetRequest->user_id = 1;

        $getPasswordResetRequestService->shouldReceive('get')
            ->once()
            ->with('dGVzdEBleGFtcGxlLmNvbQ==', 'reset-token-12345')
            ->andReturn($resetRequest);

        $request = Mockery::mock(\Illuminate\Http\Request::class);

        $controller = new UserController(
            $createUserService,
            $loginService,
            $passwordRecoveryService,
            $getPasswordResetRequestService,
            $passwordChangeService
        );

        $response = $controller->validatePasswordRecovery(
            $request,
            'reset-token-12345',
            'dGVzdEBleGFtcGxlLmNvbQ=='
        );

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('S001', $data['code']);
    }

    public function testPasswordChangeSuccessfully(): void
    {
        $createUserService = Mockery::mock(CreateUserService::class);
        $loginService = Mockery::mock(LoginService::class);
        $passwordRecoveryService = Mockery::mock(PasswordRecoveryService::class);
        $getPasswordResetRequestService = Mockery::mock(GetPasswordResetRequestService::class);
        $passwordChangeService = Mockery::mock(PasswordChangeService::class);

        $passwordChangeService->shouldReceive('change')
            ->once()
            ->with(1, 1, 'NewPassword123!')
            ->andReturnNull();

        $request = Mockery::mock(PasswordChangeRequest::class);
        $request->shouldReceive('validated')
            ->with('id')
            ->andReturn(1);
        $request->shouldReceive('validated')
            ->with('userId')
            ->andReturn(1);
        $request->shouldReceive('validated')
            ->with('newPassword')
            ->andReturn('NewPassword123!');

        $controller = new UserController(
            $createUserService,
            $loginService,
            $passwordRecoveryService,
            $getPasswordResetRequestService,
            $passwordChangeService
        );

        $response = $controller->passwordChange($request);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('S001', $data['code']);
        $this->assertNull($data['data']);
    }
}
