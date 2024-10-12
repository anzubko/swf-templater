<?php
declare(strict_types=1);

namespace SWF;

use Closure;
use SWF\Exception\TemplaterException;
use SWF\PostProcessor\HTMLDebugger;
use SWF\PostProcessor\HTMLMinifier;
use Throwable;

class NativeTemplater extends AbstractTemplater
{
    /**
     * @param string $dir Directory with templates.
     * @param bool $minify Minify processed templates.
     * @param bool $debug Makes minified template more readable.
     * @param mixed[] $globals Global variables.
     * @param Closure[] $functions Closure functions.
     */
    public function __construct(
        protected string $dir,
        protected bool $minify = false,
        protected bool $debug = false,
        protected array $globals = [],
        protected array $functions = [],
    ) {
        $this->functions['h'] ??= function (?string $string): string {
            if (null === $string) {
                return '';
            }

            return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        };

        $this->functions['u'] ??= function (?string $string): string {
            if (null === $string) {
                return '';
            }

            return urlencode($string);
        };

        $this->functions['j'] ??= function (?string $string): string {
            if (null === $string) {
                return '""';
            }

            return str_replace(' ', '\\x20', (string) json_encode($string, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG));
        };
    }

    /**
     * @inheritDoc
     *
     * @param mixed[]|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, ?array $data = null): TransformedTemplate
    {
        $timer = gettimeofday(true);

        $normalizedFilename = $this->normalizeFilename($filename, 'php', $this->dir);

        if (!ob_start(fn() => null)) {
            throw new TemplaterException('Unable to turn on output buffering');
        }

        try {
            new NativeIsolator($normalizedFilename->getFilename(), $data + $this->globals, $this->functions);
        } catch (Throwable $e) {
            while (ob_get_length()) {
                ob_end_clean();
            }

            throw (new TemplaterException($e->getMessage()))->setFileAndLine($e->getFile(), $e->getLine());
        }

        $body = (string) ob_get_clean();

        if ($this->minify && 'text/html' === $normalizedFilename->getType()) {
            if ($this->debug) {
                $body = HTMLDebugger::transform($body);
            } else {
                $body = HTMLMinifier::transform($body);
            }
        }

        $this->incTimerAndCounter(gettimeofday(true) - $timer);

        return new TransformedTemplate($body, $normalizedFilename->getType());
    }
}
