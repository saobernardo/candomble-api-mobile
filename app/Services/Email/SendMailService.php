<?php

namespace App\Services\Email;

use App\DTO\SendEmailDTO;
use App\Exceptions\ClientException;
use App\Exceptions\ConfigurationException;
use App\Exceptions\ServerException;
use Exception;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail;
use SendGrid\Response;
use STDClass;

/**
 * Class SendMailService
 *
 * Service for sending transactional emails via SendGrid.
 * Supports HTML content, attachments, BCC, and campaign-specific API keys.
 */
class SendMailService
{
    public const DEFAULT_MAIL_SENDER_NAME = 'Candomblé';
    public const DEFAULT_MAIL_SENDER = 'noreply@candomble.com.br';
    public const DEFAULT_MAIL_BCC = 'noreply@candomble.com.br';

    protected $sendgridInstance;

    /**
     * Sends an email through SendGrid.
     *
     * @param  SendEmailDTO  $dto
     *
     * @return void
     *
     * @throws ConfigurationException If no SendGrid API key is available.
     */
    public function handle(SendEmailDTO $dto): void
    {
        $tos = is_array($dto->to) ? $dto->to : explode(',', $dto->to);
        $from = $dto->from;
        $bcc = $dto->bcc ?? self::DEFAULT_MAIL_BCC;
        $subject = $dto->subject;
        $html = $dto->html;
        $attachments = $dto->attachments ?? null;
        $campaignSlug = $dto->campaignSlug ?? null;

        $sendgridConfig = config('mail.sendgridConfig');
        if (empty($sendgridConfig)) {
            throw new ConfigurationException('C045', 'missing sendgrid API Key', 'API Key do sendgrid não encontrado');
        }

        if (isset($campaignSlug)) {
            $sendgridConfig = env('SENDGRID_' . $campaignSlug . '_API_KEY');
        }

        foreach ($tos as $email) {
            $sendgrid = $this->prepareEmail($from, $subject, $email, $html, $bcc, $attachments);

            try {
                $response = $this->send($sendgrid, $sendgridConfig);

                if (intval($response->statusCode() / 100) != 2) {
                    throw new ClientException(
                        'C011',
                        'request rejected by Sendgrid servers. HTTP error: ' . $response->statusCode(),
                        'requisição rejeitada pelos servidores do sendgrid. HTTP error: ' . $response->statusCode()
                    );
                }

                if (intval($response->statusCode()) == 202) {
                    $log = new STDClass;
                    $log->event = 'SENDGRID_EXCEPTION';
                    $log->group = 'MAIL_SERVICE';
                    $log->message = 'sendgrid_wait';
                    $log->data = [
                        'response' => $response,
                        'mail' => $email,
                    ];
                    Log::info('sendgrid_wait', [json_encode($log)]);
                }
            } catch (Exception $e) {
                Log::info('[Send mail] error sending email', [
                    'message' => $e->getMessage(),
                    'exception' => $e->getTraceAsString(),
                    'payload' => $dto->toArray(),
                ]);

                throw new ServerException('C010', 'error sending email', 'erro ao enviar email');
            }
        }
    }

    /**
     * Prepare and configure a SendGrid Mail instance.
     *
     * @param  string  $from  The sender email address or formatted string.
     * @param  string  $subject  The email subject.
     * @param  string  $email  The recipient email address.
     * @param  string  $html  The HTML content of the email.
     * @param  string|array<string>  $bcc  One or more BCC email addresses.
     * @param  array<int, array{file: string, name: string}>|null  $attachments  Optional list of attachments.
     *
     * @return Mail The configured SendGrid Mail instance.
     */
    private function prepareEmail(string $from, string $subject, string $email, string $html, string $bcc, ?array $attachments = null): Mail
    {
        $sendgrid = new Mail;

        $sendgrid->setSubject($subject);

        $sendgrid->addTo($email, '');

        if (!$from || !is_string($from)) {
            $sendgrid->setFrom(self::DEFAULT_MAIL_SENDER, self::DEFAULT_MAIL_SENDER_NAME);
        } elseif (strpos($from, '<') > -1) {
            $from = explode('<', $from);
            $sendgrid->setFrom(str_replace('>', '', $from[1]), $from[0]);
        } else {
            $sendgrid->setFrom($from, '');
        }

        if ($bcc) {
            if (!is_array($bcc)) {
                $bcc = [$bcc];
            }

            foreach ($bcc as $b) {
                if (strpos($b, '<') !== false) {
                    $b = explode('<', str_replace('>', '', $b));
                    $sendgrid->addBcc(trim($b[1]), trim($b[0]));
                } else {
                    $sendgrid->addBcc($b, '');
                }
            }
        }

        $sendgrid->addContent('text/html', $html);

        if ($attachments) {
            foreach ($attachments as $att) {
                if (!isset($att['file']) || !isset($att['name'])) {
                    continue;
                }

                $dataAtt = explode('base64,', $att['file']);

                $info = $dataAtt[0];
                $dataAtt = $dataAtt[1];

                $type = explode(';', explode(':', $info)[1])[0];

                $sendgrid->addAttachment($dataAtt, $type, $att['name'], 'attachment');
            }
        }

        return $sendgrid;
    }

    /**
     * Send an email using the SendGrid API.
     *
     * Creates a SendGrid client instance with the provided configuration and
     * sends the given Mail object, returning the API response.
     *
     * @param  Mail  $sendgrid  The prepared SendGrid Mail instance.
     * @param  array  $sendgridConfig  Configuration array containing the SendGrid API key.
     *
     * @return Response The response returned by the SendGrid API.
     *
     * @throws SendGrid\Exception When the SendGrid client fails to send the email.
     */
    private function send(Mail $sendgrid, array $sendgridConfig): Response
    {
        $sendgridInstance = new SendGrid($sendgridConfig['sendgridApiKey']);

        return $sendgridInstance->send($sendgrid);
    }
}
