<?php

namespace Skalar\Api;

use Skalar\Routing\AbstractApi;

/**
 * Class MyApi
 * @package Skalar\Api
 */
class MyApi extends AbstractApi
{
    /**
     * @var string
     */
    protected $controllersFolder = 'myapi';
    /**
     * @var string
     */
    protected $libFolder = "";

    /**
     * @return array
     */
    public function getRoutes()
    {
        $routes = [];

        $files = $this->getAllFolderFiles();

        foreach ($files as $file) {
            require_once($this->templateFolder . "/" . $this->controllersFolder . "/" . $file);

            $class = "\\Skalar\\MyApi\\" . pathinfo($file, PATHINFO_FILENAME);

            $reflectionClass = new \ReflectionClass($class);
            $className = $reflectionClass->getName();
            $methods = $reflectionClass->getMethods();

            if (!empty($methods) && is_array($methods)) {
                foreach($methods as $method) {
                    if (
                        $method->getDeclaringClass()->getName() == $className
                        && $this->methodList[strtolower($method->name)]
                    ) {
                        $path = '/myapi/' . strtolower($reflectionClass->getShortName()) . '/';
                        $routeName = $method->name . $className;

                        $defaults = [
                            '_controller' => $className . '::' . $method->name,
                        ];

                        $routes[$routeName] = $this->addRoute(
                            $path,
                            $defaults,
                            $method->name
                        );
                    }
                }
            }
        }

        return $routes;
    }

}