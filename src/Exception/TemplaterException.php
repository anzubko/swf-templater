<?php
declare(strict_types=1);

namespace SWF\Exception;

use Exception;

class TemplaterException extends Exception
{
    /**
     * Sets file and line in which the exception occurred.
     */
    public function setFileAndLine(string $file, int $line): static
    {
        $this->file = $file;
        $this->line = $line;

        return $this;
    }
}
