<?php

declare(strict_types=1);

namespace Difra;

/**
 * Class Logger
 * @package Difra\Tools\Logger
 */
class Logger
{
    /**
     * Add log record
     * @param string $message
     * @param int $level
     * @param string $log
     * @throws \Difra\Exception
     */
    public static function log(string $message, int $level = self::INFO, string $log = self::DEFAULT_LOG)
    {
        $logger = Logger\Common::get($log);
        $logger->write($message, $level ?: self::INFO);
    }

    /** Default log name */
    protected const DEFAULT_LOG = 'default';
    /**
     * Logging levels
     */
    public const TRACE = 1;
    public const DEBUG = 2;
    public const INFO = 3;
    public const WARN = 4;
    public const ERROR = 5;
    public const FATAL = 6;
    public const TYPE_NONE = 'none';
    public const TYPE_STDOUT = 'stdout';
    public const TYPE_FILE = 'file';
    public const TYPE_MONGO = 'mongo';
    public const TYPE_SYSLOG_UDP = 'syslog_udp';
}
