<?php

declare(strict_types=1);

namespace Difra\Logger;

/**
 * Class None
 * @package Difra\Logger
 */
class None extends Common {
    /**
     * @inheritdoc
     */
    protected function realWrite(string $message, int $level): void
    {
    }
}
