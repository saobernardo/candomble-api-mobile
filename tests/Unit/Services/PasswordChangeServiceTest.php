<?php

namespace Tests\Unit\Services;

use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Services\PasswordService;
use App\Services\User\DeletePasswordResetTokenService;
use App\Services\User\GetUserService;
use App\Services\User\PasswordChangeService;
use App\Services\User\UpdateUserService;
use Mockery;
use Tests\TestCase;

class PasswordChangeServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testChangePasswordSuccessfully(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);
        $updateUserService = Mockery::mock(UpdateUserService::class);
        $deletePasswordResetTokenService = Mockery::mock(DeletePasswordResetTokenService::class);

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->password = '$2y$10$oldhash';

        $getUserService->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($user);

        $passwordService->shouldReceive('hash')
            ->once()
            ->with('NewPassword123!')
            ->andReturn('$2y$10$newhash');

        $updateUserService->shouldReceive('updatePassword')
            ->once()
            ->with($user, '$2y$10$newhash')
            ->andReturn($user);

        $deletePasswordResetTokenService->shouldReceive('delete')
            ->once()
            ->with(1)
            ->andReturn(true);

        $service = new PasswordChangeService(
            $getUserService,
            $passwordService,
            $updateUserService,
            $deletePasswordResetTokenService
        );

        $service->change(1, 1, 'NewPassword123!');

        $this->assertTrue(true);
    }

    public function testChangePasswordThrowsExceptionWhenUserNotFound(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $passwordService = Mockery::mock(PasswordService::class);
        $updateUserService = Mockery::mock(UpdateUserService::class);
        $deletePasswordResetTokenService = Mockery::mock(DeletePasswordResetTokenService::class);

        $getUserService->shouldReceive('getById')
            ->once()
            ->with(999)
            ->andThrow(new NotFoundException('C046', 'user not found', 'usuário não encontrado'));

        $service = new PasswordChangeService(
            $getUserService,
            $passwordService,
            $updateUserService,
            $deletePasswordResetTokenService
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('user not found');

        $service->change(1, 999, 'NewPassword123!');
    }

}
