<?php

declare(strict_types=1);

namespace Difra\Logger;

/**
 * Class File
 * @package Difra\Logger
 */
class File extends Common
{
    /** @var ?string Log file name */
    protected ?string $file = null;

    /**
     * @inheritdoc
     */
    protected function realWrite(string $message, int $level): void
    {
        file_put_contents($this->config['file'], $this->format($message) . "\n", FILE_APPEND | LOCK_EX);
    }
}
