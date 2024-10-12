<?php
declare(strict_types=1);

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
     * @param string|null $cache Cache directory.
     * @param bool $strict Whether to ignore invalid variables in templates.
     * @param bool $debug Sets reload to true.
     * @param bool $reload Reload the template if the original source changed.
     * @param mixed[] $globals Global variables.
     * @param Closure[] $functions Closure functions.
     *
     * @throws TemplaterException
     */
    public function __construct(
        string $dir,
        ?string $cache = null,
        bool $strict = true,
        bool $debug = false,
        bool $reload = false,
        protected array $globals = [],
        array $functions = [],
    ) {
        try {
            $this->twig = new TwigEnvironment(new TwigFilesystemLoader($dir), [
                'debug' => $debug,
                'cache' => $cache ?? false,
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
     * @param mixed[]|null $data
     *
     * @throws TemplaterException
     */
    public function transform(string $filename, ?array $data = null): TransformedTemplate
    {
        $timer = gettimeofday(true);

        $normalizedFilename = $this->normalizeFilename($filename, 'twig');

        try {
            $body = $this->twig->render($normalizedFilename->getFilename(), $data + $this->globals);
        } catch (Throwable $e) {
            throw (new TemplaterException($e->getMessage()))->setFileAndLine($e->getFile(), $e->getLine());
        }

        $this->incTimerAndCounter(gettimeofday(true) - $timer);

        return new TransformedTemplate($body, $normalizedFilename->getType());
    }
}
