<?php

namespace App\Services\User;

use App\Exceptions\ClientException;
use App\Services\Email\PasswordRecoveryEmailService;
use App\Services\GenerateLinksService;
use App\Services\GenerateTokensService;
use Illuminate\Support\Facades\Log;

/**
 * Service responsible for handling password recovery requests.
 *
 * Flow:
 * - Retrieve the user by email
 * - Generate a password reset token
 * - Generate the password recovery link
 * - Persist the reset token
 * - Dispatch the password recovery event
 *
 * If the user does not exist or the token cannot be saved,
 * the process is silently aborted and logged.
 */
class PasswordRecoveryService
{
    /**
     * PasswordRecoveryService constructor
     *
     * @param  GetUserService  $getUserService
     * @param  GenerateTokensService  $generateTokensService
     * @param  GenerateLinksService  $generateLinksService
     * @param  SavePasswordResetTokenService  $savePasswordResetTokenService
     * @param  PasswordRecoveryEmailService  $passwordRecoveryEmailService
     */
    public function __construct(
        protected GetUserService $getUserService,
        protected GenerateTokensService $generateTokensService,
        protected GenerateLinksService $generateLinksService,
        protected SavePasswordResetTokenService $savePasswordResetTokenService,
        protected PasswordRecoveryEmailService $passwordRecoveryEmailService
    ) {}

    /**
     * Request a password reset for a user.
     *
     * If the user does not exist, the request is ignored to avoid
     * revealing whether the email is registered in the system.
     *
     * @param  string  $email  User email requesting password reset.
     *
     * @return void
     */
    public function requestPasswordChange(string $email): void
    {
        $configMail = config('mail.from');

        $user = $this->getUserService->getByEmail($email);
        if (!$user) {
            Log::info('[User - PasswordRecoveryService] user not found', [
                'email' => $email,
            ]);

            return;
        }

        $token = $this->generateTokensService->generateUserPasswordResetRequestToken();

        $passwordRecoveryLink = $this->generateLinksService->generateRecoveryLink($email, $token, 'user');

        try {
            $this->savePasswordResetTokenService->save($user->id, $email, $token);
        } catch (ClientException) {
            Log::info('[user - PasswordRecoveryService] error registering password request', [
                'email' => $email,
            ]);

            return;
        }

        $this->passwordRecoveryEmailService->send(
            $email,
            $configMail['address'],
            $passwordRecoveryLink,
            $user
        );
    }
}
