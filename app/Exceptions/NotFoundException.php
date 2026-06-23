<?php

namespace App\Exceptions;

/**
 * Class NotFoundException
 *
 * Exception thrown when a requested resource cannot be found.
 *
 * Represents a client error with HTTP status code 404 (Not Found).
 */
class NotFoundException extends BaseException
{
    protected int $httpcode = 404;
    protected string $customMessage = 'Not found';
    protected string $customCode = 'C005';
    protected ?string $userMessage = 'Não encontrado';
}
