<?php declare(strict_types=1);

namespace SWF;

use DOMDocument;
use SWF\Exception\TemplaterException;
use SWF\Utility\ArrayToSXE;
use XSLTProcessor;

class XsltTemplater extends AbstractTemplater
{
    /**
     * @var XSLTProcessor[]
     */
    protected array $processors;

    /**
     * @param string $dir Directory with templates.
     * @param string $root Name for root element.
     * @param string $item Name for numeric element.
     * @param mixed[] $globals Global variables.
     */
    public function __construct(
        protected string $dir,
        protected string $root = 'root',
        protected string $item = 'item',
        protected array $globals = [],
    ) {
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

        $filename = $this->normalizeFilename($filename, 'xsl', $this->dir);

        if (!isset($this->processors[$filename])) {
            $doc = new DOMDocument();
            if (!$doc->load($filename, LIBXML_NOCDATA)) {
                throw new TemplaterException('XSL loading error');
            }

            $processor = new XSLTProcessor();
            if (!$processor->importStylesheet($doc)) {
                throw new TemplaterException('XSL import error');
            }

            $this->processors[$filename] = $processor;
        }

        $sxe = ArrayToSXE::transform($data + $this->globals, $this->root, $this->item);

        $contents = $this->processors[$filename]->transformToXML($sxe) ?? '';
        if (false === $contents) {
            throw new TemplaterException('XSL transform error');
        }

        self::$timer += gettimeofday(true) - $timer;

        self::$counter += 1;

        return $contents;
    }
}
