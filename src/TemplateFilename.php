<?php
declare(strict_types=1);

namespace SWF;

/**
 * @internal
 */
final readonly class TemplateFilename
{
    public function __construct(
        private string $filename,
        private string $type,
    ) {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
