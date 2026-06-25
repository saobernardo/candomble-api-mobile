<?php

namespace App\DTO;

/**
 * Class SendEmailDTO
 *
 * Data Transfer Object (DTO) representing the structure of an email message
 * to be sent through the system.
 */
class SendEmailDTO extends BaseDTO
{
    public array $to;
    public string $from;
    public ?string $bcc;
    public string $subject;
    public string $html;
    public ?array $attachments;
    public ?string $campaignSlug;
}
