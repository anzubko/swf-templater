<?php
declare(strict_types=1);

namespace SWF;

use AllowDynamicProperties;
use ArgumentCountError;
use Closure;
use SWF\Exception\TemplaterException;
use Throwable;
use TypeError;
use ValueError;

#[AllowDynamicProperties]
class NativeIsolator
{
    /**
     * @var Closure[]
     */
    private array $__functions = [];

    /**
     * @param mixed[] $__globals
     * @param Closure[] $__functions
     *
     * @throws TemplaterException
     */
    public function __construct(string $__filename, array $__globals, array $__functions)
    {
        foreach ($__globals as $__name => $__value) {
            $this->__set($__name, $__value);
        }

        foreach ($__functions as $__name => $__value) {
            $this->__set($__name, $__value);
        }

        require $__filename;

        ob_clean();

        if (isset($this->__functions['main'])) {
            $this->__call('main');
        }
    }

    /**
     * @param mixed[] $arguments
     *
     * @throws TemplaterException
     */
    public function __call(string $name, array $arguments = []): mixed
    {
        try {
            return ($this->__functions[$name] ?? null)(...$arguments); // @phpstan-ignore-line
        } catch (TemplaterException $e) {
            throw $e;
        } catch (Throwable $e) {
            if ($e->getFile() === __FILE__) {
                foreach ($e->getTrace() as $trace) {
                    if (__FILE__ === ($trace['file'] ?? '')) {
                        continue;
                    }

                    if (!isset($this->__functions[$name])) {
                        $message = sprintf('Undefined function: $this->%s()', $name);
                    } elseif ($e instanceof ArgumentCountError) {
                        $message = sprintf('Too few arguments for function: $this->%s()', $name);
                    } elseif ($e instanceof TypeError) {
                        $message = sprintf('Type error for function: $this->%s()', $name);
                    } elseif ($e instanceof ValueError) {
                        $message = sprintf('Value error for function: $this->%s()', $name);
                    } else {
                        $message = $e->getMessage();
                    }

                    throw (new TemplaterException($message))->setFileAndLine($trace['file'] ?? '', $trace['line'] ?? 0);
                }
            }

            throw (new TemplaterException($e->getMessage()))->setFileAndLine($e->getFile(), $e->getLine());
        }
    }

    public function __set(string $name, mixed $value): void
    {
        if ($value instanceof Closure) {
            $this->__functions[$name] = $value;
        } else {
            $this->{$name} = $value;
        }
    }
}
