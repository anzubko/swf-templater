<?php declare(strict_types=1);

namespace SWF;

use SWF\Interface\TemplaterInterface;

abstract class AbstractTemplater implements TemplaterInterface
{
    protected const DEFAULT_MIME = 'text/plain';

    protected const SUPPORTED_MIMES = [
        'txt' => 'text/plain',
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'rtf' => 'application/rtf',
    ];

    protected string $mime = self::DEFAULT_MIME;

    protected static float $timer = 0.0;
    protected static int $counter = 0;

    protected function normalizeFilename(string $filename, string $extension, ?string $dir = null): string
    {
        if (!str_ends_with($filename, '.' . $extension)) {
            $filename .= '.' . $extension;
        }

        if (preg_match('/\.([^.]+)\.[^.]+$/', $filename, $M)) {
            $this->mime = self::SUPPORTED_MIMES[$M[1]] ?? self::DEFAULT_MIME;
        } else {
            $this->mime = self::DEFAULT_MIME;
        }

        if (null === $dir) {
            return $filename;
        }

        return sprintf('%s/%s', $dir, $filename);
    }

    /**
     * @inheritDoc
     */
    public function getMime(): string
    {
        return $this->mime;
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
