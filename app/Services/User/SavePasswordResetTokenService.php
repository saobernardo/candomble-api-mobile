<?php

namespace App\Services\User;

use App\Exceptions\ClientException;
use App\Models\UserPasswordResetToken;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service responsible for persisting user password reset tokens.
 *
 * Creates a new password reset token entry associated with a user.
 *
 * @throws ClientException When the token cannot be saved.
 */
class SavePasswordResetTokenService
{
    /**
     * Store a password reset token for a user.
     *
     * @param  int  $userId  The user identifier.
     * @param  string  $email  The user email.
     * @param  string  $token  The generated reset token.
     *
     * @return void
     *
     * @throws ClientException
     */
    public function save(int $userId, string $email, string $token): void
    {
        try {
            $resetToken = new UserPasswordResetToken;
            $resetToken->user_id = $userId;
            $resetToken->email = $email;
            $resetToken->token = $token;
            $resetToken->save();
        } catch (Throwable $t) {
            Log::error('[CreateClientPasswordResetTokenService] insert failed', [
                'message' => $t->getMessage(),
            ]);

            throw new ClientException(
                'C047',
                'error saving user password reset token',
                'erro ao salvar requisição de troca de senha de usuário'
            );
        }
    }
}
