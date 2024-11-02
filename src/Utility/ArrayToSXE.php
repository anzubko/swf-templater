<?php
declare(strict_types=1);

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
     * @param mixed[] $array
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

        return self::transformRecursive($sxe, $array, $item);
    }

    /**
     * Base method for transform.
     *
     * @param mixed[] $array
     */
    private static function transformRecursive(SimpleXMLElement $sxe, array $array, string $item): SimpleXMLElement
    {
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                if (is_scalar($value)) {
                    $sxe->{$item}[] = $value;
                } elseif ($value instanceof SimpleXMLElement) {
                    self::transformRecursiveSXE($sxe, $value, $item);
                } elseif (is_object($value) || is_array($value)) {
                    self::transformRecursive($sxe->addChild($item), (array) $value, $item);
                }
            } elseif (str_starts_with($key, '@')) {
                $sxe[substr($key, 1)] = $value;
            } elseif (is_scalar($value)) {
                $sxe->{$key} = $value;
            } elseif ($value instanceof SimpleXMLElement) {
                self::transformRecursiveSXE($sxe, $value, $key);
            } elseif (is_object($value) || is_array($value)) {
                self::transformRecursive($sxe->addChild($key), (array) $value, $item);
            }
        }

        return $sxe;
    }

    private static function transformRecursiveSXE(SimpleXMLElement $sxe, SimpleXMLElement $object, string $name): void
    {
        $node = dom_import_simplexml($sxe->addChild($name));
        if ($node->ownerDocument === null) {
            return;
        }

        foreach (dom_import_simplexml($object)->childNodes as $child) {
            $node->appendChild($node->ownerDocument->importNode($child, true));
        }
    }
}
