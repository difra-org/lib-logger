<?php

declare(strict_types=1);

namespace Difra\Logger;

use Difra\Auth;
use Difra\Logger;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Common
 * @package Difra\Logger
 */
abstract class Common
{
    /**
     * Actually write log message
     * @param string $message
     * @param int $level
     * @return void
     * @throws \Difra\Exception
     */
    abstract protected function realWrite(string $message, int $level): void;

    /**
     * Get logger
     * @param string $log Log name
     * @return static
     */
    public static function get(string $log): static
    {
        static $instances = [];
        if (isset($instances[$log])) {
            return $instances[$log];
        }
        $config = \Difra\Config::getInstance()->getValue('logs', $log);
        $type = $config['type'] ?? Logger::TYPE_NONE;
        return match ($type) {
            Logger::TYPE_STDOUT => $instances[$log] = new Stdout($log, $config),
            Logger::TYPE_FILE => $instances[$log] = new File($log, $config),
            Logger::TYPE_MONGO => $instances[$log] = new Mongo($log, $config),
            Logger::TYPE_SYSLOG_UDP => $instances[$log] = new SyslogUDP($log, $config),
            default => $instances[$log] = new None($log, $config),
        };
    }

    /**
     * Configuration
     * @var array
     */
    protected array $config = [];
    /** @var string */
    protected string $logName = 'default';
    /**
     * @var string
     * %d Date
     * %p PID
     * %u User name
     * %m Message
     */
    protected string $logFormat = "[%d\t%p\t%a\t%u]\t%m";
    /** @var int Minimum log level */
    protected int $logLevel = Logger::INFO;

    /**
     * Write log message
     * @param string $message
     * @param int $level
     * @throws \Difra\Exception
     */
    public function write(string $message, int $level): void
    {
        if ($level < $this->logLevel) {
            return;
        }
        if (is_object($message)) {
            if (property_exists($message, '_toString')) {
                $message = (string)$message;
            } else {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
        $this->realWrite($message, $level);
    }

    /**
     * Format log string
     * @param string $message
     * @return string
     */
    protected function format(string $message): string
    {
        $obj = $this->getLogObj($message);
        $replace = [
            '%d' => $obj['date'],
            '%p' => $obj['pid'],
            '%u' => !empty($obj['user']) ? $obj['user'] : '-',
            '%a' => !empty($obj['ip']) ? $obj['ip'] : '-',
            '%m' => $obj['message'],
        ];
        $result = $this->logFormat;
        foreach ($replace as $fromStr => $toStr) {
            $result = str_replace($fromStr, $toStr, $result);
        }
        return $result;
    }

    /**
     * Get log object
     * @param string $message
     * @return array
     */
    #[ArrayShape(['timestamp' => 'int', 'date' => 'string', 'message' => '', 'pid' => 'false|int', 'user' => 'mixed|null', 'ip' => 'string'])]
    protected function getLogObj(string $message): array
    {
        $result = [
            'timestamp' => time(),
            'date' => date('r'),
            'message' => $message,
            'pid' => getmypid()
        ];
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $result['ip'] = $_SERVER['REMOTE_ADDR'];
        }
        if ($login = Auth::getInstance()->getLogin()) {
            $result['user'] = $login;
        }
        return $result;
    }

    /**
     * Constructor
     * @param string $log
     * @param array $config
     */
    private function __construct(string $log, array $config)
    {
        $this->logName = $log;
        if (!empty($config['format'])) {
            $this->logFormat = $config['format'];
        }
    }
}
