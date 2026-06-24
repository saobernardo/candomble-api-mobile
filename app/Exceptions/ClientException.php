<?php

namespace App\Exceptions;

/**
 * Class ClientException
 *
 * Exception representing a generic client-side error (HTTP 400).
 */
class ClientException extends BaseException
{
    protected int $httpcode = 400;
    protected string $customCode = 'C016';
    protected string $customMessage = 'general client error';
    protected ?string $userMessage = 'erro no client';
}
