<?php declare(strict_types=1);

namespace SWF;

use Closure;
use SWF\Exception\TemplaterException;
use Throwable;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Twig\TwigFunction;

class TwigTemplater extends AbstractTemplater
{
    protected TwigEnvironment $twig;

    /**
     * @param string $dir Directory with templates.
     * @param bool $debug Sets reload to true.
     * @param bool|string $cache Cache directory.
     * @param bool $reload Reload the template if the original source changed.
     * @param bool $strict Whether to ignore invalid variables in templates.
     * @param array<int|string,mixed> $globals Global variables.
     * @param array<string,Closure> $functions Closure functions.
     *
     * @throws TemplaterException
     */
    public function __construct(
        string $dir,
        bool $debug = false,
        bool|string $cache = false,
        bool $reload = false,
        bool $strict = false,
        protected array $globals = [],
        array $functions = [],
    ) {
        try {
            $this->twig = new TwigEnvironment(new TwigFilesystemLoader($dir), [
                'debug' => $debug,
                'cache' => $cache,
                'auto_reload' => $reload,
                'strict_variables' => $strict,
                'autoescape' => 'name',
            ]);
        } catch (Throwable $e) {
            throw (new TemplaterException($e->getMessage()))->setFileAndLine($e->getFile(), $e->getLine());
        }

        foreach ($functions as $name => $value) {
            $this->twig->addFunction(new TwigFunction($name, $value));
        }
    }

    /**
     * @inheritDoc
     *
     * @param array<int|string,mixed>|object|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, array|object|null $data = null): string
    {
        $timer = gettimeofday(true);

        $filename = $this->normalizeFilename($filename, 'twig');

        try {
            $contents = $this->twig->render($filename, (array) $data + $this->globals);
        } catch (Throwable $e) {
            throw (new TemplaterException($e->getMessage()))->setFileAndLine($e->getFile(), $e->getLine());
        }

        self::$timer += gettimeofday(true) - $timer;

        self::$counter += 1;

        return $contents;
    }
}
