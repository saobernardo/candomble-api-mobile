<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

/**
 * Validates the incoming request for a password change operation.
 * This request ensures that the recovery token ID, the target user ID,
 * and the new password string meet the minimum application requirements
 * before reaching the service layer.
 */
class PasswordChangeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'min:1'],
            'userId' => ['required', 'integer', 'min:1'],
            'newPassword' => ['required', 'string'],
        ];
    }

    /**
     * Define the body parameters for API documentation (Scribe/OpenAPI).
     *
     * @return array<string, array{description: string, example: mixed}>
     */
    public function bodyParameters(): array
    {
        return [
            'id' => [
                'description' => 'The password request id',
                'example' => 1,
            ],
            'userId' => [
                'description' => 'The user ID for identification',
                'example' => 1,
            ],
            'newPassword' => [
                'description' => 'The new password to be changed',
                'example' => 'cabelinNaRegua@2025',
            ],
        ];
    }
}
