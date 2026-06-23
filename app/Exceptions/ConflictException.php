<?php

namespace App\Exceptions;

/**
 * Class ConflictException
 *
 * Exception thrown when a request conflicts with the current state of the server,
 * such as when trying to create a resource that already exists.
 *
 * Represents an HTTP 409 Conflict error.
 */
class ConflictException extends BaseException
{
    protected int $httpcode = 409;
    protected string $customCode = 'C004';
    protected string $customMessage = 'conflict';
    protected ?string $userMessage = 'conflito';
}
