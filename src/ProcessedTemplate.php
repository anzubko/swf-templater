<?php declare(strict_types=1);

namespace SWF;

final readonly class ProcessedTemplate
{
    public function __construct(
        private string $body,
        private string $type,
    ) {
    }

    /**
     * Gets body of processed template.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Gets mime type of processed template.
     */
    public function getType(): string
    {
        return $this->type;
    }
}
