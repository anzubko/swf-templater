<?php
declare(strict_types=1);

namespace SWF;

use SWF\Interface\TemplaterInterface;

abstract class AbstractTemplater implements TemplaterInterface
{
    private float $timer = 0.0;

    private int $counter = 0;

    /**
     * @inheritDoc
     */
    public function getTimer(): float
    {
        return $this->timer;
    }

    /**
     * @inheritDoc
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    protected function incTimerAndCounter(float $timer): void
    {
        $this->timer += $timer;
        TemplaterRegistry::$timer += $timer;

        $this->counter++;
        TemplaterRegistry::$counter++;
    }

    protected function normalizeFilename(string $filename, string $extension, ?string $dir = null): TemplateFilename
    {
        $dotExtension = sprintf('.%s', $extension);
        if (!str_ends_with($filename, $dotExtension)) {
            $filename = $filename . $dotExtension;
        }

        $type = null;
        if (preg_match('/\.([^.]+)\.[^.]+$/', $filename, $M)) {
            $type = TemplaterRegistry::$supportedTypesByExtensions[$M[1]] ?? null;
        }

        if (null !== $dir) {
            $filename = sprintf('%s/%s', $dir, $filename);
        }

        return new TemplateFilename($filename, $type ?? TemplaterRegistry::$defaultType);
    }
}
