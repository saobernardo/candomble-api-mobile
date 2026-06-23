<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;

/**
 * Class CreateUserDTO
 *
 * Data Transfer Object responsible for carrying
 * user creation data from request layer to service layer.
 *
 * @property string $email
 * @property string|null $cpf
 * @property string|null $rg
 * @property string $password
 * @property string $fullName
 * @property string|null $googleId
 */
class CreateUserDTO extends BaseDTO
{
    public string $email;
    public ?string $cpf;
    public ?string $rg;
    public string $password;
    public string $fullName;
    public ?string $googleId;
    public ?string $facebookId;

    /**
     * Create a DTO instance from raw array data.
     *
     * Maps primitive fields directly and converts nested
     * address and contact arrays into their respective DTO objects.
     *
     * @param  array<string, mixed>  $data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dto = new self;
        $dto->email = $data['email'];
        $dto->cpf = $data['cpf'] ?? null;
        $dto->rg = $data['rg'] ?? null;
        $dto->password = $data['password'];
        $dto->fullName = $data['fullName'];
        $dto->googleId = $data['googleId'] ?? null;
        $dto->facebookId = $data['facebookId'] ?? null;

        return $dto;
    }
}
