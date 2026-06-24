<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Service responsible for generating application tokens.
 *
 * Currently used for generating password reset request tokens.
 */
class GenerateTokensService
{
    /**
     * Generate a random token for a user password reset request.
     *
     * @return string Generated token with 30 random characters.
     */
    public function generateUserPasswordResetRequestToken(): string
    {
        return Str::random(30);
    }

    /**
     * Generate a random token for a client password reset request.
     *
     * Creates a secure random string that can be used as a token
     * to validate password reset requests.
     *
     * @return string The generated password reset token.
     */
    public function generateClientPasswordResetRequestToken(): string
    {
        return Str::random(30);
    }
}
