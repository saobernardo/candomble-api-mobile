<?php

namespace App\Exceptions;

/**
 * Class ServerException
 *
 * Exception representing a generic client-side error (HTTP 400).
 */
class ServerException extends BaseException
{
    protected int $httpcode = 500;
    protected string $customCode = 'C003';
    protected string $customMessage = 'general server error';
    protected ?string $userMessage = 'erro no servidor';
}
