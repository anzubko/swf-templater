<?php
declare(strict_types=1);

namespace SWF;

/**
 * @internal
 */
final readonly class TemplateFilename
{
    public function __construct(
        public string $filename,
        public string $type,
    ) {
    }
}
