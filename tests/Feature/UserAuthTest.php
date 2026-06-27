<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation with valid data
     */
    public function testCreateUserWithValidData(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'fullName' => 'Test User',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ];

        $response = $this->postJson('/auth/create', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'code',
                'message',
                'userMessage',
                'data' => [
                    'email',
                    'fullName',
                    'cpf',
                ],
            ])
            ->assertJson([
                'code' => 'S001',
                'message' => 'success',
            ]);

        $this->assertDatabaseHas('user.user', [
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'cpf' => '12345678901',
        ]);
    }

    /**
     * Test user creation with invalid data
     */
    public function testCreateUserWithInvalidData(): void
    {
        $userData = [
            'email' => 'invalid-email',
            'fullName' => '',
            'password' => '123',
        ];

        $response = $this->postJson('/auth/create', $userData);

        $response->assertStatus(400);
    }

    /**
     * Test user login with valid credentials
     */
    public function testLoginWithValidCredentials(): void
    {
        $password = 'Password123!';
        $user = User::create([
            'email' => 'login@example.com',
            'full_name' => 'Login User',
            'cpf' => '98765432100',
            'password' => Hash::make($password),
            'activated' => true,
        ]);

        $response = $this->postJson('/auth/login', [
            'email' => 'login@example.com',
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'userMessage',
                'data' => [
                    'jwtToken',
                ],
            ])
            ->assertJson([
                'code' => 'S001',
                'message' => 'success',
            ]);
    }

    /**
     * Test user login with invalid credentials
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $user = User::create([
            'email' => 'login@example.com',
            'full_name' => 'Login User',
            'cpf' => '98765432100',
            'password' => Hash::make('Password123!'),
            'activated' => true,
        ]);

        $response = $this->postJson('/auth/login', [
            'email' => 'login@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test password recovery request with valid email
     */
    public function testPasswordRecoveryRequestWithValidEmail(): void
    {
        $user = User::create([
            'email' => 'recovery@example.com',
            'full_name' => 'Recovery User',
            'cpf' => '11122233344',
            'password' => Hash::make('Password123!'),
            'activated' => true,
        ]);

        $response = $this->postJson('/auth/password-recovery-request', [
            'email' => 'recovery@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'code' => 'S001',
                'message' => 'success',
                'data' => null,
            ]);
    }

    /**
     * Test password recovery request with non-existent email
     */
    public function testPasswordRecoveryRequestWithNonExistentEmail(): void
    {
        $response = $this->postJson('/auth/password-recovery-request', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'code' => 'S001',
                'message' => 'success',
            ]);
    }

    /**
     * Test validate password recovery with valid token
     */
    public function testValidatePasswordRecoveryWithValidToken(): void
    {
        $user = User::create([
            'email' => 'validate@example.com',
            'full_name' => 'Validate User',
            'cpf' => '55566677788',
            'password' => Hash::make('Password123!'),
            'activated' => true,
        ]);

        $this->postJson('/auth/password-recovery-request', [
            'email' => 'validate@example.com',
        ]);

        $passwordResetRequest = \DB::connection('mysql-user')
            ->table('user.password_reset_request')
            ->where('user_id', $user->id)
            ->first();

        $encodedEmail = base64_encode('validate@example.com');
        $token = $passwordResetRequest->token;

        $response = $this->getJson("/auth/validate-password-recovery/{$token}/{$encodedEmail}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message',
                'userMessage',
                'data' => [
                    'email',
                    'token',
                ],
            ])
            ->assertJson([
                'code' => 'S001',
                'message' => 'success',
            ]);
    }

    /**
     * Test validate password recovery with invalid token
     */
    public function testValidatePasswordRecoveryWithInvalidToken(): void
    {
        $encodedEmail = base64_encode('test@example.com');
        $invalidToken = 'invalid-token-12345';

        $response = $this->getJson("/auth/validate-password-recovery/{$invalidToken}/{$encodedEmail}");

        $response->assertStatus(404);
    }

    /**
     * Test password change with valid data
     */
    public function testPasswordChangeWithValidData(): void
    {
        $user = User::create([
            'email' => 'change@example.com',
            'full_name' => 'Change User',
            'cpf' => '99988877766',
            'password' => Hash::make('OldPassword123!'),
            'activated' => true,
        ]);

        $this->postJson('/auth/password-recovery-request', [
            'email' => 'change@example.com',
        ]);

        $passwordResetRequest = \DB::connection('mysql-user')
            ->table('user.password_reset_request')
            ->where('user_id', $user->id)
            ->first();

        $response = $this->putJson('/auth/password-change', [
            'id' => $passwordResetRequest->id,
            'userId' => $user->id,
            'newPassword' => 'NewPassword123!',
            'newPasswordConfirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'code' => 'S001',
                'message' => 'success',
                'data' => null,
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    /**
     * Test password change with invalid data
     */
    public function testPasswordChangeWithInvalidData(): void
    {
        $response = $this->putJson('/auth/password-change', [
            'id' => 99999,
            'userId' => 99999,
            'newPassword' => 'NewPassword123!',
            'newPasswordConfirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test password change with mismatched passwords
     */
    public function testPasswordChangeWithMismatchedPasswords(): void
    {
        $user = User::create([
            'email' => 'mismatch@example.com',
            'full_name' => 'Mismatch User',
            'cpf' => '44455566677',
            'password' => Hash::make('OldPassword123!'),
            'activated' => true,
        ]);

        $this->postJson('/auth/password-recovery-request', [
            'email' => 'mismatch@example.com',
        ]);

        $passwordResetRequest = \DB::connection('mysql-user')
            ->table('user.password_reset_request')
            ->where('user_id', $user->id)
            ->first();

        $response = $this->putJson('/auth/password-change', [
            'id' => $passwordResetRequest->id,
            'userId' => $user->id,
            'newPassword' => 'NewPassword123!',
            'newPasswordConfirmation' => 'DifferentPassword123!',
        ]);

        $response->assertStatus(400);
    }
}
