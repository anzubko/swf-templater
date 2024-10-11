<?php
declare(strict_types=1);

namespace SWF\PostProcessor;

use function strlen;

abstract class AbstractPostProcessor
{
    /**
     * Minifies all.
     */
    public static function transform(string $contents): string
    {
        preg_match_all('~<(script|style|t)\b[^>]*+>.*?</\1>~is', $contents, $matches, flags: PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $pos = 0;
        $chunks = [];
        foreach ($matches as $M) {
            $chunks[] = static::between(substr($contents, $pos, (int) $M[0][1] - $pos));

            $tag = strtolower($M[1][0]);

            $chunks[] = static::$tag($M[0][0]);

            $pos = (int) $M[0][1] + strlen($M[0][0]);
        }

        $chunks[] = static::between(substr($contents, $pos));
        $chunks[] = "\n";

        return implode($chunks);
    }

    /**
     * Special tag <t> preserves all spaces inside, but removes itself.
     */
    protected static function t(string $chunk): string
    {
        return substr($chunk, strpos($chunk, '>') + 1, -4);
    }

    /**
     * Minifies javascript.
     */
    abstract protected static function script(string $chunk): string;

    /**
     * Minifies styles.
     */
    abstract protected static function style(string $chunk): string;

    /**
     * Minifies all between matches.
     */
    abstract protected static function between(string $chunk): string;
}
