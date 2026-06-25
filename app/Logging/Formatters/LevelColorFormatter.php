<?php

namespace App\Logging\Formatters;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Class LevelColorFormatter
 *
 * Custom Monolog formatter that colors log levels for terminal output.
 * Each log level is displayed in a distinct color to improve readability.
 *
 * Colors are applied only to the level name; the message and context remain standard.
 */
class LevelColorFormatter implements FormatterInterface
{
    /**
     * @var array<string, string> Mapping of log levels to their respective ANSI color codes.
     */
    private array $levelColors = [
        'DEBUG' => "\033[0;37m",     // Gray
        'INFO' => "\033[0;36m",     // Cyan
        'NOTICE' => "\033[1;34m",     // Bright Blue
        'WARNING' => "\033[1;33m",     // Bright Yellow (laranja aproximado)
        'ERROR' => "\033[0;31m",     // Red
        'CRITICAL' => "\033[1;35m",     // Magenta
        'ALERT' => "\033[1;31;43m",  // Bold Red + Yellow background
        'EMERGENCY' => "\033[1;41m",     // Bold Red background
    ];

    /**
     * Formats a single log record.
     *
     * @param  LogRecord  $record  The log record to format.
     *
     * @return string The formatted log line with color applied to the level name.
     */
    public function format(LogRecord $record): string
    {
        $levelName = $record->level->getName();
        $color = $this->levelColors[$levelName] ?? "\033[0m";

        $datetime = $record->datetime->format('Y-m-d H:i:s');

        $coloredLevel = $color . $levelName . "\033[0m";

        $message = $record->message;

        $context = '';
        if (!empty($record->context)) {
            $contextJson = json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $context = "\n" . $contextJson;
        }

        return sprintf(
            "\033[0;32m[%s]\033[0m %s: %s%s\n\n",
            $datetime,
            $coloredLevel,
            $message,
            $context
        );
    }

    /**
     * Formats a batch of log records.
     *
     * @param  LogRecord[]  $records  An array of log records.
     *
     * @return string Concatenated formatted log lines.
     */
    public function formatBatch(array $records): string
    {
        $output = '';
        foreach ($records as $record) {
            $output .= $this->format($record);
        }

        return $output;
    }
}
