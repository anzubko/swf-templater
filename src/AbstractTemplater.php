<?php declare(strict_types=1);

namespace SWF;

use SWF\Interface\TemplaterInterface;

abstract class AbstractTemplater implements TemplaterInterface
{
    protected const DEFAULT_TYPE = 'text/plain';

    protected const SUPPORTED_TYPES_BY_EXTENSIONS = [
        'txt' => 'text/plain',
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'rtf' => 'application/rtf',
    ];

    protected string $type = self::DEFAULT_TYPE;

    protected static float $timer = 0.0;

    protected static int $counter = 0;

    protected function normalizeFilename(string $filename, string $extension, ?string $dir = null): string
    {
        if (!str_ends_with($filename, '.' . $extension)) {
            $filename .= '.' . $extension;
        }

        if (preg_match('/\.([^.]+)\.[^.]+$/', $filename, $M)) {
            $this->type = self::SUPPORTED_TYPES_BY_EXTENSIONS[$M[1]] ?? self::DEFAULT_TYPE;
        } else {
            $this->type = self::DEFAULT_TYPE;
        }

        if (null === $dir) {
            return $filename;
        }

        return sprintf('%s/%s', $dir, $filename);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getTimer(): float
    {
        return self::$timer;
    }

    /**
     * @inheritDoc
     */
    public function getCounter(): int
    {
        return self::$counter;
    }
}
