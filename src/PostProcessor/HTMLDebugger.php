<?php declare(strict_types=1);

namespace SWF\PostProcessor;

class HTMLDebugger extends AbstractPostProcessor
{
    /**
     * @inheritDoc
     */
    protected static function script(string $chunk): string
    {
        return $chunk;
    }

    /**
     * @inheritDoc
     */
    protected static function style(string $chunk): string
    {
        return $chunk;
    }

    /**
     * @inheritDoc
     */
    protected static function between(string $chunk): string
    {
        $chunk = (string) preg_replace_callback('~<[a-z][^>]+>~i', fn($M) => (string) preg_replace('/\s+/u', ' ', $M[0]), $chunk);

        $chunk = (string) preg_replace('/>(\s+)</u', '><!--\1--><', $chunk);

        $chunk = (string) preg_replace('/^(\s+)/u', '<!--\1-->', $chunk);

        $chunk = (string) preg_replace('/(\s+)$/u', '<!--\1-->', $chunk);

        return str_replace('--><!--', '       ', $chunk);
    }
}
