<?php
declare(strict_types=1);

namespace SWF;

final readonly class TransformedTemplate
{
    public function __construct(
        public string $body,
        public string $type,
    ) {
    }
}
