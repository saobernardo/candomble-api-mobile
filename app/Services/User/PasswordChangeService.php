<?php

namespace App\Services\User;

use App\Services\PasswordService;
use Exception;

/**
 * Orchestration service to handle the full password change workflow.
 * * Coordinates user retrieval, password hashing, record updating,
 * and post-update cleanup of the reset token.
 */
class PasswordChangeService
{
    /**
     * PasswordChangeService constructor
     *
     * @param  GetUserService  $getUserService
     * @param  PasswordService  $passwordService
     * @param  UpdateUserService  $updateUserService
     * @param  DeletePasswordResetTokenService  $deletePasswordResetTokenService
     */
    public function __construct(
        protected GetUserService $getUserService,
        protected PasswordService $passwordService,
        protected UpdateUserService $updateUserService,
        protected DeletePasswordResetTokenService $deletePasswordResetTokenService
    ) {}

    /**
     * Executes the password change process.
     *
     * @param  int  $passwordRequestId  The ID of the used reset token to be deleted.
     * @param  int  $userId  The ID of the user whose password is being changed.
     * @param  string  $newPassword  The plain-text new password to be hashed.
     *
     * @return void
     *
     * @throws Exception If the user is not found, hashing fails, or token deletion errors occur.
     */
    public function change(int $passwordRequestId, int $userId, string $newPassword): void
    {
        $user = $this->getUserService->getById($userId);
        $hashedPassword = $this->passwordService->hash($newPassword);
        $user = $this->updateUserService->updatePassword($user, $hashedPassword);

        $this->deletePasswordResetTokenService->delete($passwordRequestId);
    }
}
