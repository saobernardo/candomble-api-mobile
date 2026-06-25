<?php

namespace App\Exceptions;

/**
 * Class ConfigurationException
 *
 * Exception thrown when a required configuration is missing or invalid.
 *
 * Represents a server-side error with HTTP status code 500 (Internal Server Error).
 */
class ConfigurationException extends BaseException
{
    protected int $httpcode = 500;
    protected string $customCode = 'C006';
    protected string $customMessage = 'config not found';
    protected ?string $userMessage = 'configuração não encontrada';
}
