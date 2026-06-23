<?php

namespace App\Services\User;

use App\DTO\User\CreateUserDTO;
use App\Exceptions\ConflictException;
use App\Exceptions\InvalidParamException;
use App\Exceptions\ServerException;
use App\Models\User;
use App\Services\PasswordService;
use App\Traits\DocumentsValidationTrait;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class CreateUserService
 *
 * Responsible for orchestrating the complete user creation flow,
 * including validation, persistence, and related entities (addresses and contacts).
 */
class CreateUserService
{
    use DocumentsValidationTrait;

    /**
     * CreateUserService constructor
     *
     * @param  GetUserService  $getUserService
     * @param  PasswordService  $passwordService
     */
    public function __construct(
        protected GetUserService $getUserService,
        protected PasswordService $passwordService,
    ) {}

    /**
     * Executes the complete user creation flow.
     *
     * Validates business rules, persists the user and related entities
     * (addresses and contacts) inside a database transaction.
     *
     * @param  CreateUserDTO  $dto  Data required to create the user and related entities.
     *
     * @return User The newly created user instance.
     *
     * @throws ServerException When an unexpected error occurs during creation.
     */
    public function create(CreateUserDTO $dto): User
    {
        $this->validateUser($dto);

        try {
            $user = $this->saveUser($dto);
        } catch (Throwable $t) {
            Log::info('[CreateUSerService] error creating user account', [
                'message' => $t->getMessage(),
                'email' => $dto->email,
            ]);

            throw new ServerException('C044', 'error creating user', 'erro ao criar usuário', [
                'message' => $t->getMessage(),
                'email' => $dto->email,
            ]);
        }

        return $user;
    }

    /**
     * Validates business rules before creating the user.
     *
     * @param  CreateUserDTO  $dto  Data to be validated prior to persistence.
     *
     * @return void
     *
     * @throws InvalidParamException When email is missing or CPF is invalid.
     * @throws ConflictException When a user with the same email already exists.
     */
    public function validateUser(CreateUserDTO $dto): void
    {
        if (empty($dto->email)) {
            throw new InvalidParamException('C041', 'user email not provided', 'email de usuário não fornecido');
        }

        if (!empty($dto->cpf) && !$this->isCPFValid($dto->cpf)) {
            throw new InvalidParamException('C042', 'invalid CPF', 'CPF inválido');
        }

        if ($this->checkClientExists($dto)) {
            throw new ConflictException('C043', 'user already exists', 'user already exists');
        }
    }

    /**
     * Checks whether an activated user already exists with the given email.
     *
     * @param  CreateUserDTO  $dto  Contains the email used to verify existence.
     *
     * @return bool True if an activated user exists, otherwise false.
     */
    public function checkClientExists(CreateUserDTO $dto): bool
    {
        $existingUser = $this->getUserService->getByEmail($dto->email);

        if (!empty($existingUser) && $existingUser->activated) {
            return true;
        }

        return false;
    }

    /**
     * Persists the main user entity.
     *
     * @param  CreateUserDTO  $dto  Data used to populate the User model.
     *
     * @return User The saved User instance.
     */
    public function saveUser(CreateUserDTO $dto): User
    {
        $user = new User;
        $user->email = $dto->email;
        $user->full_name = $dto->fullName;
        $user->cpf = $dto->cpf;
        $user->rg = $dto->rg;
        $user->password = $this->passwordService->hash($dto->password);
        $user->activated = true;
        $user->save();

        return $user;
    }
}
