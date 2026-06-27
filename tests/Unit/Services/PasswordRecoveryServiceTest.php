<?php

namespace Tests\Unit\Services;

use App\Exceptions\ClientException;
use App\Models\User;
use App\Services\Email\PasswordRecoveryEmailService;
use App\Services\GenerateLinksService;
use App\Services\GenerateTokensService;
use App\Services\User\GetUserService;
use App\Services\User\PasswordRecoveryService;
use App\Services\User\SavePasswordResetTokenService;
use Mockery;
use Tests\TestCase;

class PasswordRecoveryServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testRequestPasswordChangeSuccessfully(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $generateTokensService = Mockery::mock(GenerateTokensService::class);
        $generateLinksService = Mockery::mock(GenerateLinksService::class);
        $savePasswordResetTokenService = Mockery::mock(SavePasswordResetTokenService::class);
        $passwordRecoveryEmailService = Mockery::mock(PasswordRecoveryEmailService::class);

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        $generateTokensService->shouldReceive('generateUserPasswordResetRequestToken')
            ->once()
            ->andReturn('reset-token-12345');

        $generateLinksService->shouldReceive('generateRecoveryLink')
            ->once()
            ->with('test@example.com', 'reset-token-12345', 'user/auth')
            ->andReturn('https://example.com/reset?token=reset-token-12345');

        $savePasswordResetTokenService->shouldReceive('save')
            ->once()
            ->with(1, 'test@example.com', 'reset-token-12345')
            ->andReturnNull();

        $passwordRecoveryEmailService->shouldReceive('send')
            ->once()
            ->andReturnNull();

        config(['mail.from.address' => 'noreply@example.com']);

        $service = new PasswordRecoveryService(
            $getUserService,
            $generateTokensService,
            $generateLinksService,
            $savePasswordResetTokenService,
            $passwordRecoveryEmailService
        );

        $service->requestPasswordChange('test@example.com');

        $this->assertTrue(true);
    }

    public function testRequestPasswordChangeReturnsEarlyWhenUserNotFound(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $generateTokensService = Mockery::mock(GenerateTokensService::class);
        $generateLinksService = Mockery::mock(GenerateLinksService::class);
        $savePasswordResetTokenService = Mockery::mock(SavePasswordResetTokenService::class);
        $passwordRecoveryEmailService = Mockery::mock(PasswordRecoveryEmailService::class);

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(null);

        $generateTokensService->shouldNotReceive('generateUserPasswordResetRequestToken');
        $generateLinksService->shouldNotReceive('generateRecoveryLink');
        $savePasswordResetTokenService->shouldNotReceive('save');
        $passwordRecoveryEmailService->shouldNotReceive('send');

        $service = new PasswordRecoveryService(
            $getUserService,
            $generateTokensService,
            $generateLinksService,
            $savePasswordResetTokenService,
            $passwordRecoveryEmailService
        );

        $service->requestPasswordChange('nonexistent@example.com');

        $this->assertTrue(true);
    }

    public function testRequestPasswordChangeReturnsEarlyWhenTokenSaveFails(): void
    {
        $getUserService = Mockery::mock(GetUserService::class);
        $generateTokensService = Mockery::mock(GenerateTokensService::class);
        $generateLinksService = Mockery::mock(GenerateLinksService::class);
        $savePasswordResetTokenService = Mockery::mock(SavePasswordResetTokenService::class);
        $passwordRecoveryEmailService = Mockery::mock(PasswordRecoveryEmailService::class);

        $user = new User();
        $user->id = 1;
        $user->email = 'test@example.com';

        $getUserService->shouldReceive('getByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        $generateTokensService->shouldReceive('generateUserPasswordResetRequestToken')
            ->once()
            ->andReturn('reset-token-12345');

        $generateLinksService->shouldReceive('generateRecoveryLink')
            ->once()
            ->with('test@example.com', 'reset-token-12345', 'user/auth')
            ->andReturn('https://example.com/reset?token=reset-token-12345');

        $savePasswordResetTokenService->shouldReceive('save')
            ->once()
            ->with(1, 'test@example.com', 'reset-token-12345')
            ->andThrow(new ClientException('C001', 'error', 'erro'));

        $passwordRecoveryEmailService->shouldNotReceive('send');

        config(['mail.from.address' => 'noreply@example.com']);

        $service = new PasswordRecoveryService(
            $getUserService,
            $generateTokensService,
            $generateLinksService,
            $savePasswordResetTokenService,
            $passwordRecoveryEmailService
        );

        $service->requestPasswordChange('test@example.com');

        $this->assertTrue(true);
    }
}
