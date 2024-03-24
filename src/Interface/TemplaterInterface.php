<?php declare(strict_types=1);

namespace SWF\Interface;

use SWF\Exception\TemplaterException;

interface TemplaterInterface
{
    /**
     * Transforming template to page.
     *
     * @param mixed[]|object|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, array|object|null $data = null): string;

    /**
     * Gets last template mime type.
     */
    public function getMime(): string;

    /**
     * Gets timer of processed templates.
     */
    public function getTimer(): float;

    /**
     * Gets count of processed templates.
     */
    public function getCounter(): int;
}
