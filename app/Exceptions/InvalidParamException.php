<?php

namespace App\Exceptions;

/**
 * Class InvalidParamException
 *
 * Exception thrown when invalid parameters are encountered in a request.
 *
 * Represents a client error with HTTP status code 400 (Bad Request).
 */
class InvalidParamException extends BaseException
{
    protected int $httpcode = 400;
    protected string $customMessage = 'invalid params';
    protected ?string $userMessage = 'parâmetro(s) inválido(s)';
    protected string $customCode = 'C002';
}
