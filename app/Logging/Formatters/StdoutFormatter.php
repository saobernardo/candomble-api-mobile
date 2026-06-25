<?php

namespace App\Logging\Formatters;

use DateTimeInterface;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

/**
 * Custom JSON formatter for stdout logging.
 */
final class StdoutFormatter extends JsonFormatter
{
    /**
     * Normalize record and format to gcp pattern
     *
     * @param  LogRecord  $record
     *
     * @return array
     */
    protected function normalizeRecord(LogRecord $record): array
    {
        $normalized = parent::normalizeRecord($record);

        $normalized['severity'] = $normalized['level_name'];
        $normalized['time'] = $record->datetime->format(DateTimeInterface::RFC3339_EXTENDED);

        unset($normalized['level'], $normalized['level_name'], $normalized['datetime']);

        $normalized['httpRequest'] = $this->formatRequest();
        $normalized['logType'] = 'application';
        $normalized['labels'] = $this->getLabels();

        return $normalized;
    }

    /**
     * Format httpRequest information
     *
     * @return array
     */
    private function formatRequest(): array
    {
        return [
            'requestMethod' => Request::getMethod() ?? null,
            'requestUrl' => URL::current() ?? null,
            'userAgent' => Request::server('HTTP_USER_AGENT') ?? null,
            'remoteIp' => Request::ip() ?? null,
            'protocol' => Request::server('SERVER_PROTOCOL') ?? null,
        ];
    }

    /**
     * Returns application labels used for log classification.
     *
     * @return array<string, string> The log labels.
     */
    private function getLabels(): array
    {
        return [
            'environment' => config('app.env'),
            'service' => config('app.name'),
            'type' => 'APPLICATION_LOG',
        ];
    }
}
