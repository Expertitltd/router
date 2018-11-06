<?php
namespace Skalar;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Route;

/**
 * Class AdvancedLoader
 * @package Skalar
 */
class AdvancedLoader extends YamlFileLoader
{
    /**
     * @var array
     */
    private $files;

    /**
     * AdvancedLoader constructor.
     * @param FileLocatorInterface $locator
     * @param array $files
     */
    public function __construct(FileLocatorInterface $locator, array $files = [])
    {
        $this->files = $files;
        parent::__construct($locator);
    }

    /**
     * @param string $resource
     * @param null $type
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function load($resource, $type = null)
    {
        $collection = parent::load($resource, $type);

        foreach ($this->files as $file) {
            $reflectionClass = new \ReflectionClass('\\' . ltrim(str_replace('.php', '', $file), '\\'));
            $methods = $reflectionClass->getMethods();
            if (!empty($methods) && is_array($methods)) {
                foreach($methods as $method) {
                    $path = '/rest/' . strtolower($method->class);
                    $defaults = [
                        '_controller' => $method->class . '::' . $method->name,
                    ];
                    $route = new Route($path, $defaults);
                    $route->setMethods([$method->name]);
                    $collection->add($method->name . $method->class, $route);
                }
            }
        }

        return $collection;
    }

}