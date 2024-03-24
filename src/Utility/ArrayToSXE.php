<?php declare(strict_types=1);

namespace SWF\Utility;

use Exception;
use SWF\Exception\TemplaterException;
use SimpleXMLElement;
use function is_array;
use function is_int;
use function is_object;
use function is_scalar;

class ArrayToSXE
{
    /**
     * Transforms array to SimpleXMLElement.
     *
     * @param array<int|string,mixed> $array
     *
     * @throws TemplaterException
     */
    public static function transform(array $array, string $root = 'root', string $item = 'item'): SimpleXMLElement
    {
        try {
            $sxe = new SimpleXMLElement(sprintf('<?xml version="1.0" encoding="utf-8"?><%s />', $root));
        } catch (Exception $e) {
            throw new TemplaterException($e->getMessage());
        }

        return static::transformRecursive($array, $item, $sxe);
    }

    /**
     * Base method for transform.
     *
     * @param array<int|string,mixed> $array
     */
    protected static function transformRecursive(array $array, string $item, SimpleXMLElement $sxe): SimpleXMLElement
    {
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                if (is_scalar($value)) {
                    $sxe->{$item}[] = $value;
                } elseif (is_array($value)) {
                    static::transformRecursive($value, $item, $sxe->addChild($item));
                } elseif (is_object($value)) {
                    if ($value instanceof SimpleXMLElement) {
                        $node = dom_import_simplexml($sxe->addChild($item));
                        if (null !== $node->ownerDocument) {
                            foreach (dom_import_simplexml($value)->childNodes as $child) {
                                $node->appendChild($node->ownerDocument->importNode($child, true));
                            }
                        }
                    } else {
                        static::transformRecursive((array) $value, $item, $sxe->addChild($item));
                    }
                }
            } else {
                if (str_starts_with($key, '@')) {
                    $sxe[substr($key, 1)] = $value;
                } elseif (is_scalar($value)) {
                    $sxe->{$key} = $value;
                } elseif (is_array($value)) {
                    static::transformRecursive($value, $item, $sxe->addChild($key));
                } elseif (is_object($value)) {
                    if ($value instanceof SimpleXMLElement) {
                        $node = dom_import_simplexml($sxe->addChild($key));
                        if (null !== $node->ownerDocument) {
                            foreach (dom_import_simplexml($value)->childNodes as $child) {
                                $node->appendChild($node->ownerDocument->importNode($child, true));
                            }
                        }
                    } else {
                        static::transformRecursive((array) $value, $item, $sxe->addChild($key));
                    }
                }
            }
        }

        return $sxe;
    }
}
