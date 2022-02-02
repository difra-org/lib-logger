<?php

namespace Difra\Logger;

use Difra\Envi;

/**
 * Class SyslogUDP
 * @package Difra\Logger
 */
class SyslogUDP extends Common
{
    protected const DEFAULT_HOST = '127.0.0.1';
    protected const DEFAULT_PORT = '514';

    /**
     * @inheritdoc
     */
    protected function realWrite(string $message, int $level): void
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $date = date('c');
        $host = $this->config['hostname'] ?? Envi::getHost(true);
        foreach (explode("\n", $message) as $line) {
            $syslog_message = "<22>$date $host {$this->logName}:$line";
            socket_sendto(
                $socket,
                $syslog_message,
                strlen($syslog_message),
                0,
                $this->config['host'] ?? self::DEFAULT_HOST,
                $this->config['port'] ?? self::DEFAULT_PORT
            );
        }
        socket_close($socket);
    }
}
