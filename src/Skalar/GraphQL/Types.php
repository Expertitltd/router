<?php

namespace Skalar\GraphQL;

/**
 * Class Types
 * @package Skalar\Type
 */
class Types
{
    private static $namespace = "\\Skalar\\Type\\";
    private static $types = [];
    public static function __callStatic($name, $arguments)
    {
        $class = self::$namespace . ucfirst($name);
        return self::$types[$name] ?: (self::$types[$name] = self::instantiateClass($class));
    }
    private static function instantiateClass($class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return new $class();
    }
}