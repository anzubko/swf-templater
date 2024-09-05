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

        return static::transformRecursive($sxe, $array, $item);
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
                } elseif (is_array($value)) {
                    static::transformRecursive($sxe->addChild($item), $value, $item);
                } elseif (is_object($value)) {
                    self::transformRecursiveObject($sxe, $value, $item, $item);
                }
            } else {
                if (str_starts_with($key, '@')) {
                    $sxe[substr($key, 1)] = $value;
                } elseif (is_scalar($value)) {
                    $sxe->{$key} = $value;
                } elseif (is_array($value)) {
                    static::transformRecursive($sxe->addChild($key), $value, $item);
                } elseif (is_object($value)) {
                    self::transformRecursiveObject($sxe, $value, $key, $item);
                }
            }
        }

        return $sxe;
    }

    private static function transformRecursiveObject(SimpleXMLElement $sxe, object $object, string $name, string $item): void
    {
        if ($object instanceof SimpleXMLElement) {
            $node = dom_import_simplexml($sxe->addChild($name));
            if (null !== $node->ownerDocument) {
                foreach (dom_import_simplexml($object)->childNodes as $child) {
                    $node->appendChild($node->ownerDocument->importNode($child, true));
                }
            }
        } else {
            static::transformRecursive($sxe->addChild($name), (array) $object, $item);
        }
    }
}
