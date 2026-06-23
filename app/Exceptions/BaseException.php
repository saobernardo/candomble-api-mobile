<?php

namespace App\Exceptions;

use App\Enums\LogSeverityEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Base exception class for API error handling
 *
 * Provides standardized exception formatting with:
 * - HTTP status codes
 * - Custom error codes
 * - Technical messages (for developers/logs)
 * - User-friendly messages
 * - Additional error data payload
 * - Log severity levels
 */
class BaseException extends Exception
{
    /**
     * Exception http code
     *
     * @var int
     */
    protected int $httpcode = 500;

    /**
     * Custom message showed by default
     *
     * @var string
     */
    protected string $customMessage = 'server error';

    /**
     * Custom code showed by default
     *
     * @var ?string
     */
    protected string $customCode = 'I001';

    /**
     * Errors triggered
     *
     * @var ?array
     */
    protected ?array $data = null;

    /**
     * Exception log severity
     *
     * @var LogSeverityEnum
     */
    protected LogSeverityEnum $logSeverity = LogSeverityEnum::ERROR;

    /**
     * Message to the user
     *
     * @var ?string
     */
    protected ?string $userMessage = null;

    /**
     * Create a new exception instance.
     *
     * @param  string|null  $customCode
     * @param  string|null  $message
     * @param  string|null  $userMessage
     * @param  array|null  $errors
     * @param  LogSeverityEnum|null  $logSeverity
     * @param  Throwable|null  $previous
     */
    public function __construct(
        ?string $customCode = null,
        ?string $message = null,
        ?string $userMessage = null,
        ?array $errors = null,
        ?LogSeverityEnum $logSeverity = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $this->httpcode, $previous);

        $this->setCustomCode($customCode);
        $this->setMessage();
        $this->setUserMessage($userMessage);
        $this->setData($errors);
        $this->setLogSeverity($logSeverity);
    }

    /**
     * Set custom code
     *
     * Get default custom code case param is empty
     *
     * @param  string|null  $customCode
     *
     * @return void
     */
    protected function setCustomCode(?string $customCode): void
    {
        if ($customCode) {
            $this->customCode = $customCode;
        }
    }

    /**
     * Set message
     *
     * @return void
     */
    protected function setMessage(): void
    {
        $this->message = empty($this->getMessage()) ? $this->customMessage : $this->getMessage();
    }

    /**
     * Set user message
     *
     * @param  string|null  $userMessage
     *
     * @return void
     */
    protected function setUserMessage(?string $userMessage): void
    {
        if ($userMessage) {
            $this->userMessage = $userMessage;
        }
    }

    /**
     * Set errors triggered
     *
     * @param  ?array  $errors
     *
     * @return void
     */
    protected function setData(?array $errors = null): void
    {
        $this->data = $errors;
    }

    /**
     * Set exception's log severity
     *
     * @param  LogSeverityEnum|null  $logSeverity
     *
     * @return void
     */
    protected function setLogSeverity(?LogSeverityEnum $logSeverity): void
    {
        if ($logSeverity) {
            $this->logSeverity = $logSeverity;
        }
    }

    /**
     * Get exception's log severity
     *
     * @return LogSeverityEnum
     */
    public function getLogSeverity(): LogSeverityEnum
    {
        return $this->logSeverity;
    }

    /**
     * Get http status code
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->httpcode;
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'exception' => get_class(($this)),
            'data' => $this->data,
        ];
    }

    /**
     * Format response
     *
     * @return array
     */
    protected function formatResponse(): array
    {
        return [
            'code' => $this->customCode,
            'message' => $this->message,
            'userMessage' => $this->userMessage,
            'data' => $this->data,
        ];
    }

    /**
     * Render the exception as a JSON response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json($this->formatResponse(), $this->getStatus());
    }

    /**
     * Get exception's data
     *
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Get user message
     *
     * @return string|null
     */
    public function getUserMessage(): ?string
    {
        return $this->userMessage;
    }

    /**
     * Get custom code
     *
     * @return string|null
     */
    public function getCustomCode(): ?string
    {
        return $this->customCode;
    }
}
