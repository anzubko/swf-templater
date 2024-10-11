<?php
declare(strict_types=1);

namespace SWF;

final class Templater
{
    /**
     * Gets timer of processed templates of all templaters.
     */
    public static function getTimer(): float
    {
        return TemplaterRegistry::$timer;
    }

    /**
     * Gets count of processed templates of all templaters.
     */
    public static function getCounter(): int
    {
        return TemplaterRegistry::$counter;
    }
}
