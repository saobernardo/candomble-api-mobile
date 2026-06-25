<?php

namespace App\Factories;

use App\Contracts\MountEmailInterface;
use App\Exceptions\InvalidParamException;
use App\Services\Email\Builds\BuildPasswordRecoveryEmailClientService;

/**
 * Factory responsible for resolving the appropriate email mounting service
 * based on the provided template type.
 *
 * Returns an implementation of MountEmailInterface that knows how to build
 * and process the specific email template.
 */
class MountEmailFactory
{
    /**
     * Create an email mounting service instance for the specified template type.
     *
     * Uses a match expression to resolve the correct implementation. If the
     * provided type does not match any known template handler, an
     * InvalidParamException is thrown.
     *
     * @param  string  $type  The email template type to resolve.
     *
     * @return MountEmailInterface The resolved email mounting service.
     *
     * @throws InvalidParamException When the template type is invalid.
     */
    public static function create(string $type): MountEmailInterface
    {
        return match ($type) {
            'passwordRecoveryRequest' => app(BuildPasswordRecoveryEmailClientService::class),
            default => throw new InvalidParamException('C007', 'invalid email template', 'template de email inválido')
        };
    }
}
