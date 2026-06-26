<?php

namespace App\Http\Controllers;

use App\DTO\User\CreateUserDTO;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\PasswordChangeRequest;
use App\Http\Requests\User\PasswordRecoveryRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\ValidatePasswordRecoveryResource;
use App\Services\User\CreateUserService;
use App\Services\User\GetPasswordResetRequestService;
use App\Services\User\LoginService;
use App\Services\User\PasswordChangeService;
use App\Services\User\PasswordRecoveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
    /**
     * UserController constructor
     *
     * @param  CreateUserService  $createUserService
     * @param  LoginService  $loginService
     * @param  PasswordRecoveryService  $passwordRecoveryService
     * @param  GetPasswordResetRequestService  $getPasswordResetRequestService
     * @param  PasswordChangeService  $passwordChangeService
     */
    public function __construct(
        protected CreateUserService $createUserService,
        protected LoginService $loginService,
        protected PasswordRecoveryService $passwordRecoveryService,
        protected GetPasswordResetRequestService $getPasswordResetRequestService,
        protected PasswordChangeService $passwordChangeService
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

    /**
     * Login
     *
     * User Login.
     *
     * @responseFile 200 resources/docs/responses/user/auth/activate200.json scenario="200 - S001"
     * @responseFile 404 resources/docs/responses/user/auth/activate404-C046.json scenario="404 - C046"
     *
     * @responseField code string The status code of the response.
     * @responseField message string A message providing additional details.
     * @responseField userMessage string A message intended for end-users.
     * @responseField data object[] A object containing the response message of succesful authentication.
     * @responseField data.jwt_token user login JWT token.
     *
     * @param  LoginRequest  $request
     *
     * @return JsonResponse JSON response with user activation result.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->loginService->login($request->validated('email'), $request->validated('password'));

        return response()->json()->default(
            code: 'S001',
            message: 'success',
            userMessage: 'sucesso',
            data: ['jwtToken' => $response],
        );
    }

    /**
     * Password Recovery Request
     *
     * The first step to user password recovery.
     *
     * @responseFile 200 resources/docs/responses/user/auth/passwordRecoveryRequest200.json scenario="200 - S001"
     *
     * @responseField code string The status code of the response.
     * @responseField message string A message providing additional details.
     * @responseField userMessage string A message intended for end-users.
     * @responseField data object|null This routes doesn't have any data.
     *
     * @param  PasswordRecoveryRequest  $request
     *
     * @return JsonResponse JSON response with user activation result.
     */
    public function passwordRecoveryRequest(PasswordRecoveryRequest $request): JsonResponse
    {
        $this->passwordRecoveryService->requestPasswordChange($request->validated('email'));

        return response()->json()->default(
            code: 'S001',
            message: 'success',
            userMessage: 'sucesso',
            data: null,
            status: 201
        );
    }

    /**
     * Validate Password Recovery
     *
     * The second step to user password recovery
     *
     * @responseFile 200 resources/docs/responses/user/auth/validatePasswordRecovery200.json scenario="200 - S001"
     * @responseFile 404 resources/docs/responses/user/auth/validatePasswordRecovery404-C019.json scenario="404 - C019"
     *
     * @responseField code string The status code of the response.
     * @responseField message string A message providing additional details.
     * @responseField userMessage string A message intended for end-users.
     * @responseField data object|null The route data response
     *
     * @respondeField data.email string The user's email
     *
     * @responseField data.token string The recovery password request token.
     *
     * @param  PasswordRecoveryRequest  $request
     * @param  string  $token
     * @param  string  $encodedEmail
     *
     * @return JsonResponse JSON response with relevant data.
     */
    public function validatePasswordRecovery(Request $request, string $token, string $encodedEmail): JsonResponse
    {
        $resetRequest = $this->getPasswordResetRequestService->get($encodedEmail, $token);

        return response()->json()->default(
            code: 'S001',
            message: 'success',
            userMessage: 'sucesso',
            data: new ValidatePasswordRecoveryResource($resetRequest),
        );
    }

    /**
     * Password Change
     *
     * The third step to user password recovery
     *
     * @responseFile 200 resources/docs/responses/user/auth/passwordChange200.json scenario="200 - S001"
     * @responseFile 404 resources/docs/responses/user/auth/passwordChange404-C046.json scenario="404 - C046"
     * @responseFile 500 resources/docs/responses/user/auth/passwordChange500-C049.json scenario="500 - C049"
     * @responseFile 500 resources/docs/responses/user/auth/passwordchange500-C050.json scenario="500 - C050"
     *
     * @responseField code string The status code of the response.
     * @responseField message string A message providing additional details.
     * @responseField userMessage string A message intended for end-users.
     * @responseField data object|null This routes doesn't have any data.
     *
     * @param  PasswordChangeRequest  $request
     *
     * @return JsonResponse JSON response with relevant data.
     */
    public function passwordChange(PasswordChangeRequest $request): JsonResponse
    {
        $this->passwordChangeService->change(
            $request->validated('id'),
            $request->validated('userId'),
            $request->validated('newPassword')
        );

        return response()->json()->default(
            code: 'S001',
            message: 'success',
            userMessage: 'sucesso',
            data: null,
            status: 201
        );
    }
}
