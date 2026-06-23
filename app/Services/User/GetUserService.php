<?php

namespace App\Services\User;

use App\Exceptions\NotFoundException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class GetUserService
 *
 * Responsible for retrieving user entities from the database.
 */
class GetUserService
{
    /**
     * Retrieve a user by their email address.
     *
     * Searches for the first user record matching the given email.
     *
     * @param  string  $email  The user's email address.
     *
     * @return User|null Returns the User instance if found, otherwise null.
     */
    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Retrieve an inactive and unverified user created within the last 24 hours.
     *
     * Searches for a user matching the given email who:
     * - is not activated
     * - has not verified their email
     * - was created within the last day
     *
     * If no matching user is found, a domain-level NotFoundException is thrown.
     *
     * @param  string  $email  The user's email address.
     *
     * @return User The inactive user matching the criteria.
     *
     * @throws NotFoundException When no matching user is found.
     */
    public function getInactiveUser(string $email): User
    {
        try {
            return User::where('email', $email)
                ->where('activated', 0)
                ->whereNull('email_verified_at')
                ->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new NotFoundException('C046', 'user not found', 'usuário não encontrado');
        }
    }

    /**
     * Finds a user by their primary ID.
     *
     * @param  int  $id  The unique identifier of the user.
     *
     * @return User The found user model.
     *
     * @throws NotFoundException If no user exists with the given ID (Error Code: C046).
     */
    public function getById(int $id): User
    {
        try {
            return User::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new NotFoundException('C046', 'user not found', 'usuário não encontrado');
        }
    }
}
