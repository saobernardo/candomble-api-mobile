<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

/**
 * Handle validation for creating a new user.
 *
 * Expected request body structure:
 *
 * @property string $email
 * @property string|null $cpf Required if rg is not provided.
 * @property string|null $rg Required if cpf is not provided.
 * @property string $password
 * @property string $fullName
 * @property string|null $googleId
 * @property string|null $facebookId
 * @property array<int, array{
 *     street: string,
 *     addressNumber: int,
 *     complement?: string|null,
 *     neighborhood: string,
 *     city: string,
 *     state: string,
 *     postalCode: string
 * }> | null $addresses
 * @property array<int, array{
 *     type: string,
 *     value: string
 * }> | null $contacts
 *
 * Validation rules:
 * - email: required, valid email format
 * - cpf: nullable, required_without:rg
 * - rg: nullable, required_without:cpf
 * - password: required, string, max:255
 * - fullName: required, string
 * - googleId: nullable, string
 * - facebookId: nullable, string
 * - addresses: nullable array
 * - contacts: nullable array
 */
class CreateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'cpf' => ['nullable', 'required_without:rg', 'string'],
            'rg' => ['nullable', 'required_without:cpf', 'string'],
            'password' => ['required', 'string', 'max:255'],
            'fullName' => ['required', 'string'],
            'googleId' => ['nullable', 'string'],
            'facebookId' => ['nullable', 'string'],
        ];
    }
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'User email address.',
                'example' => 'user@example.com',
            ],
            'cpf' => [
                'description' => 'User CPF document number.',
                'example' => '12345678900',
            ],
            'rg' => [
                'description' => 'User RG document.',
                'example' => '123456789',
            ],
            'password' => [
                'description' => 'User password.',
                'example' => 'StrongPassword123',
            ],
            'fullName' => [
                'description' => 'User full name.',
                'example' => 'Lucas São Bernardo Pinheiro',
            ],
            'googleId' => [
                'description' => 'User Google ID',
                'example' => '132',
            ],
            'facebookId' => [
                'description' => 'User Facebook ID',
                'example' => '465',
            ],

        ];
    }
}
