<?php declare(strict_types=1);

namespace SWF\Interface;

use SWF\Exception\TemplaterException;
use SWF\ProcessedTemplate;

interface TemplaterInterface
{
    /**
     * Transforming template to page.
     *
     * @param mixed[]|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, ?array $data = null): ProcessedTemplate;

    /**
     * Gets timer of processed templates.
     */
    public function getTimer(): float;

    /**
     * Gets count of processed templates.
     */
    public function getCounter(): int;
}
