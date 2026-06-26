<?php

namespace App\Services\User;

use App\Exceptions\NotFoundException;
use App\Models\UserPasswordResetToken;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Service responsible for retrieving and validating password reset requests.
 */
class GetPasswordResetRequestService
{
    /**
     * Retrieve a valid password reset token by email and token string.
     * * Validates that the request exists and was created within the last hour.
     *
     * @param  string  $encodedEmail  The email address associated with the request.
     * @param  string  $token  The unique reset token string.
     *
     * s@return UserPasswordResetToken The found token model instance.
     *
     * @throws NotFoundException If no valid token is found or if the token has expired.
     */
    public function get(string $encodedEmail, string $token): UserPasswordResetToken
    {
        $email = base64_decode(strtr($encodedEmail, '-_', '+/'));

        try {
            return UserPasswordResetToken::where('email', $email)
                ->where('token', $token)
                ->whereBetween('created_at', [Carbon::now()->subHour(), Carbon::now()])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new NotFoundException('C013', 'password recovery request not found', 'requisição de recuperação de senha não encontrado');
        }
    }

    /**
     * Retrieve a password reset token by its primary ID.
     *
     * @param  int  $id  The unique identifier of the reset token.
     *
     * @return UserPasswordResetToken The found token model.
     *
     * @throws NotFoundException If the token does not exist in the database (Error Code: C019).
     */
    public function getById(int $id): UserPasswordResetToken
    {
        try {
            return UserPasswordResetToken::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException('C013', 'password recovery request not found', 'requisição de recuperação de senha não encontrado');
        }
    }
}
