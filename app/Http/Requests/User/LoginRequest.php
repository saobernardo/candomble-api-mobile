<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

/**
 * Handle validation for user login requests.
 *
 * Expected request body:
 *
 * @property string|null $email User email address.
 * @property string $password User password.
 *
 * Validation rules:
 * - email: nullable, string
 * - password: required, string
 */
class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParams(): array
    {
        return [
            'email' => [
                'description' => "The user's email",
                'example' => 'login@email.com',
            ],
            'password' => [
                'description' => "The user's password",
                'example' => 'abcd1234',
            ],
        ];
    }
}
