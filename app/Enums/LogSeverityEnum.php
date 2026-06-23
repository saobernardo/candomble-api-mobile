<?php

namespace App\Enums;

/**
 * Enum LogSeverityEnum
 *
 * Represents severity levels for logging messages, following common PSR-3 log levels.
 */
enum LogSeverityEnum: string
{
    case EMERGENCY = 'emergency';
    case ALERT = 'alert';
    case CRITICAL = 'critical';
    case ERROR = 'error';
    case WARNING = 'warning';
    case NOTICE = 'notice';
    case INFO = 'info';
    case DEBUG = 'debug';
}
