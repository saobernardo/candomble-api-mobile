<?php

namespace App\Services\User;

use App\Exceptions\ServerException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service responsible for updating user state.
 *
 * Currently supports user activation logic.
 */
class UpdateUserService
{
    /**
     * Updates the password for a specific user.
     *
     * Sets the new hashed password, persists the change to the database,
     * and handles potential persistence errors by logging and re-throwing
     * as a domain-specific exception.
     *
     * @param  User  $user  The user instance to update.
     * @param  string  $hashedPassword  The pre-hashed password string.
     *
     * @return User The updated user instance.
     *
     * @throws ServerException If the database update fails (Error Code: C049).
     */
    public function updatePassword(User $user, string $hashedPassword): User
    {
        try {
            $user->password = $hashedPassword;
            $user->save();

            return $user;
        } catch (Exception $e) {
            Log::error('[UpdateUserService] error updating user password', [
                'message' => $e->getMessage(),
                'userId' => $user->id,
            ]);

            throw new ServerException('C049', 'error updating user password', 'erro ao atualizar senha de usuário');
        }
    }
}
