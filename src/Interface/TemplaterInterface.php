<?php declare(strict_types=1);

namespace SWF\Interface;

use SWF\Exception\TemplaterException;

interface TemplaterInterface
{
    /**
     * Transforming template to page.
     *
     * @param mixed[]|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, ?array $data = null): string;

    /**
     * Gets last template mime type.
     */
    public function getType(): string;

    /**
     * Gets timer of processed templates.
     */
    public function getTimer(): float;

    /**
     * Gets count of processed templates.
     */
    public function getCounter(): int;
}
