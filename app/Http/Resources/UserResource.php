<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Data transformer for the User model.
 *
 * This resource standardizes the output for professional/admin users,
 * including nested relationships for their primary address and contact details.
 *
 * @property string $email
 * @property string $full_name
 * @property string $cpf
 * @property string $rg
 * @property Collection $addresses
 * @property Collection $contacts
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'email' => $this->email,
            'fullName' => $this->full_name,
            'cpf' => $this->cpf,
            'rg' => $this->rg,
        ];
    }
}
