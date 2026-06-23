<?php

namespace App\Http\Controllers;

use App\DTO\User\CreateUserDTO;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\User\CreateUserService;
use Illuminate\Http\JsonResponse;

class UserController
{
    public function __construct(
        protected CreateUserService $createUserService
    ) {}

    /**
     * Create
     *
     * Handles the creation of a new user address information.
     *
     * @responseFile 201 resources/docs/responses/user/auth/create201.json scenario="201 - S001"
     * @responseFile 400 resources/docs/responses/user/auth/create400-V001.json scenario="400 - V001"
     * @responseFile 500 resources/docs/responses/user/auth/create500-C044.json scenario="500 - C044"
     *
     * @responseField code string Application response code.
     * @responseField message string Technical response message.
     * @responseField userMessage string User-friendly localized message.
     * @responseField data object Main response payload.
     * @responseField data.email string User email address.
     * @responseField data.fullName string User full name.
     * @responseField data.cpf string Brazilian CPF document number.
     * @responseField data.rg string|null Brazilian RG document number.
     * @responseField data.address|null object User primary address.
     * @responseField data.address.street string Street name.
     * @responseField data.address.number integer Address number.
     * @responseField data.address.complement string|null Address complement.
     * @responseField data.address.neighborhood string Neighborhood name.
     * @responseField data.address.city string City name.
     * @responseField data.address.state string State abbreviation.
     * @responseField data.address.postal_code string Postal code.
     * @responseField data.contacts|null object User primary contact.
     * @responseField data.contacts.type string Contact type (e.g. PHONE).
     * @responseField data.contacts.value string Contact value.
     *
     * @param  CreateUserRequest  $request  Validated request containing user data.
     *
     * @return JsonResponse JSON response with user creation result.
     */
    public function create(CreateUserRequest $request): JsonResponse
    {
        $dto = CreateUserDTO::fromArray($request->validated());

        $user = $this->createUserService->create($dto);

        return response()->json()->default(
            code: 'S001',
            message: 'success',
            userMessage: 'sucesso',
            data: new UserResource($user),
            status: 201
        );
    }
}
