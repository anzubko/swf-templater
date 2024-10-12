<?php
declare(strict_types=1);

namespace SWF;

final readonly class TransformedTemplate
{
    public function __construct(
        private string $body,
        private string $type,
    ) {
    }

    /**
     * Gets body of transformed template.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Gets mime type of transformed template.
     */
    public function getType(): string
    {
        return $this->type;
    }
}
