<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

/**
 * Handle validation for password recovery requests.
 *
 * Expected request body:
 *
 * @property string $email Email address used to request password recovery.
 *
 * Validation rules:
 * - email: required, valid email format
 */
class PasswordRecoveryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    /**
     * Body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'User email for recovery',
                'example' => 'loremepsilum@email.com',
            ],
        ];
    }
}
