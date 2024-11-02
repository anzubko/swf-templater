<?php
declare(strict_types=1);

namespace SWF\PostProcessor;

class HTMLMinifier extends AbstractPostProcessor
{
    /**
     * @inheritDoc
     */
    protected static function script(string $chunk): string
    {
        return (string) preg_replace("/[ \t\n\r\v\f]+/", ' ', $chunk);
    }

    /**
     * @inheritDoc
     */
    protected static function style(string $chunk): string
    {
        return (string) preg_replace("/[ \t\n\r\v\f]+/", ' ', $chunk);
    }

    /**
     * @inheritDoc
     */
    protected static function between(string $chunk): string
    {
        $chunk = trim((string) preg_replace('/<!--.*?-->/s', '', $chunk));
        if ($chunk === '') {
            return '';
        }

        return str_replace('> <', '><', (string) preg_replace("/[ \t\n\r\v\f]+/", ' ', $chunk));
    }
}
