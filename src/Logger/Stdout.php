<?php

namespace Difra\Logger;

/**
 * Class Stdout
 * @package Drafton\Logger
 */
class Stdout extends Common
{
    /**
     * @inheritdoc
     */
    protected function realWrite(string $message, int $level): void
    {
        echo $this->format($message), PHP_EOL;
    }
}
