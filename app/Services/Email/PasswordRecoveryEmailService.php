<?php

namespace App\Services\Email;

use App\DTO\SendEmailDTO;
use App\Factories\MountEmailFactory;
use App\Models\User;

/**
 * Class PasswordRecoveryEmailService
 *
 * Service responsible for sending password recovery emails using a predefined template.
 */
class PasswordRecoveryEmailService
{
    private const TYPE = 'passwordRecoveryRequest';

    /**
     * PasswordRecoveryEmailService Constructor.
     *
     * @param  SendMailService  $sendMailService
     */
    public function __construct(
        protected SendMailService $sendMailService,
    ) {}

    /**
     * Sends a password recovery email with the configured HTML template.
     *
     * @param  string  $to
     * @param  string  $from
     * @param  string  $passwordRecoveryLink
     * @param  User  $access
     *
     * @return void
     */
    public function send(string $to, string $from, string $passwordRecoveryLink, User $access): void
    {
        $params = $this->buildParameters($access, $passwordRecoveryLink);

        $service = MountEmailFactory::create(self::TYPE);
        $emailHTML = $service->mountHTML(self::TYPE, $params);
        $subject = 'Candomblé - Recuperação de senha';

        $dto = $this->toDTO([$to], $from, $subject, $emailHTML);

        $this->sendMailService->handle($dto);
    }

    /**
     * Convert an associative array of email parameters into a SendEmailDTO instance.
     *
     * @param  array  $to
     * @param  string  $from
     * @param  string  $subject
     * @param  string  $html
     *
     * @return SendEmailDTO The populated email DTO instance.
     */
    private function toDTO(array $to, string $from, string $subject, string $html): SendEmailDTO
    {
        $dto = new SendEmailDTO;
        $dto->to = $to;
        $dto->from = $from;
        $dto->subject = $subject;
        $dto->html = $html;

        return $dto;
    }

    /**
     * Build template parameters for password recovery emails.
     *
     * Generates a standardized parameter array used for email template rendering,
     * extracting the appropriate name field depending on whether the access
     * belongs to a client or a user.
     *
     * @param  User  $access  The authenticated entity (user or client).
     * @param  string  $passwordRecoveryLink  The generated password recovery URL.
     *
     * @return array{name: string, url: string} The parameters to be injected into the email template.
     */
    private function buildParameters(User $access, string $passwordRecoveryLink): array
    {
        return [
            'name' => $access->full_name,
            'url' => $passwordRecoveryLink,
        ];
    }
}
