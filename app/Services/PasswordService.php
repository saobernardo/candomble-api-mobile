<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

/**
 * Class PasswordService
 *
 * Provides password-related utilities such as hashing.
 */
class PasswordService
{
    /**
     * Hashes a plaintext password using Laravel's hashing system.
     *
     * @param  string  $unhashedPassword
     *
     * @return string The hashed password.
     */
    public function hash(string $unhashedPassword): string
    {
        $passwordConfig = config('password');

        return Hash::make($unhashedPassword, [
            'rounds' => $passwordConfig['rounds'],
        ]);
    }

    /**
     * Verifies if a plain text password matches a hashed password.
     *
     * @param  string  $unhashedPassword  The plain text password to verify.
     * @param  string  $hashedPassword  The hashed password to compare against.
     *
     * @return bool True if the passwords match, false otherwise.
     */
    public function check(string $unhashedPassword, string $hashedPassword): bool
    {
        return Hash::check($unhashedPassword, $hashedPassword);
    }
}
