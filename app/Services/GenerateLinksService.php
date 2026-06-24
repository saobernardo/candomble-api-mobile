<?php

namespace App\Services;

/**
 * Service responsible for generating application links.
 *
 * Currently used to generate password recovery links.
 */
class GenerateLinksService
{
    /**
     * Generate a password recovery URL for a user.
     *
     * The generated URL contains:
     * - The recovery token
     * - The base64 encoded email of the user
     *
     * @param  string  $email  User email address.
     * @param  string  $token  Password recovery token.
     * @param  string  $prefix
     *
     * @return string Fully qualified password recovery URL.
     */
    public function generateRecoveryLink(string $email, string $token, string $prefix): string
    {
        $appConfiguration = config('app');

        $url = $appConfiguration['url'];
        $emailBase64 = rtrim(strtr(base64_encode($email), '+/', '-_'));

        $url = $url . "/{$prefix}/validatePasswordRecovery/{$token}/{$emailBase64}";

        return $url;
    }
}
