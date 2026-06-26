<?php

namespace App\Services\User;

use App\Exceptions\ServerException;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service responsible for removing password reset tokens from the database.
 */
class DeletePasswordResetTokenService
{
    /**
     * DeletePasswordResetTokenService constructor
     *
     * @param  GetPasswordResetRequestService  $getPasswordResetRequestService
     */
    public function __construct(
        protected GetPasswordResetRequestService $getPasswordResetRequestService
    ) {}

    /**
     * Delete a password reset token by its unique ID.
     *
     * @param  int  $id  The primary key of the token to be deleted.
     *
     * @return bool True if the deletion was successful.
     *
     * @throws ServerException If the deletion fails or a database error occurs.
     */
    public function delete(int $id): bool
    {
        try {
            return $this->getPasswordResetRequestService->getById($id)->delete();
        } catch (Exception $e) {
            Log::error('[DeletePasswordResetTokenService] error deleting password recovery request', [
                'message' => $e->getMessage(),
                'id' => $id,
            ]);

            throw new ServerException('C012', 'error deleting password recovery', 'erro ao excluir recuperação de senha');
        }
    }
}
