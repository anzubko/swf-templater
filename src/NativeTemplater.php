<?php declare(strict_types=1);

namespace SWF;

use Closure;
use SWF\Exception\TemplaterException;
use SWF\Native\NativeDebugger;
use SWF\Native\NativeIsolator;
use SWF\Native\NativeMinifier;

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

            $flags = JSON_UNESCAPED_UNICODE | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG;

            return str_replace(' ', '\\x20', (string) json_encode($string, $flags));
        };
    }

    /**
     * @inheritDoc
     *
     * @param mixed[]|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, ?array $data = null): string
    {
        $timer = gettimeofday(true);

        $filename = $this->normalizeFilename($filename, 'php', $this->dir);

        ob_start(fn() => null);

        try {
            new NativeIsolator($filename, $data + $this->globals, $this->functions);
        } catch (TemplaterException $e) {
            throw (new TemplaterException($e->getMessage()))->setFileAndLine($e->getFile(), $e->getLine());
        }

        $contents = (string) ob_get_clean();

        if ($this->minify && 'text/html' === $this->type) {
            if ($this->debug) {
                $contents = NativeDebugger::transform($contents);
            } else {
                $contents = NativeMinifier::transform($contents);
            }
        }

        self::$timer += gettimeofday(true) - $timer;

        self::$counter += 1;

        return $contents;
    }
}
