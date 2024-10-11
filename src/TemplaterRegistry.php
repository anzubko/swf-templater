<?php
declare(strict_types=1);

namespace SWF;

/**
 * @internal
 */
final class TemplaterRegistry
{
    public static float $timer = 0.0;

    public static int $counter = 0;

    public static string $defaultType = 'text/plain';

    /**
     * @var string[]
     */
    public static array $supportedTypesByExtensions = [
        'txt' => 'text/plain',
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'rtf' => 'application/rtf',
    ];
}
