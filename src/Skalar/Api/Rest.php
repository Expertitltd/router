<?php

namespace Skalar\Api;

use Skalar\Routing\AbstractApi;
use Symfony\Component\Routing\Route;

/**
 * Class RestRouter
 * @package Skalar\Rest
 */
class Rest extends AbstractApi
{
    /**
     * @var string
     */
    protected $controllersFolder = 'rest';

    /**
     * @var array
     */
    protected $params = [
        '',
        'param1',
        'param2',
    ];

    protected $methodList = [
        "get" => "get",
        "post" => "post",
        "put" => "put",
    ];

    /**
     * @return array|mixed
     * @throws \ReflectionException
     */
    public function getRoutes()
    {
        $routes = [];
        $files = $this->getAllFolderFiles();

        foreach ($files as $file) {
            require_once($this->templateFolder . "/" . $this->controllersFolder . "/" . $file);

            $class = "\\Skalar\\Rest\\" . pathinfo($file, PATHINFO_FILENAME);

            $reflectionClass = new \ReflectionClass($class);
            $className = $reflectionClass->getName();
            $methods = $reflectionClass->getMethods();

            if (!empty($methods) && is_array($methods)) {
                foreach($methods as $method) {
                    if (
                        $method->getDeclaringClass()->getName() == $className
                        && $this->methodList[strtolower($method->name)]
                    ) {
                        $path = '/rest/' . strtolower($reflectionClass->getShortName()) . '/';
                        $routeName = $method->name . $className;

                        foreach ($this->params as $param) {
                            if (!empty($param)) {
                                $path .= '{' . $param . '}/';
                                $routeName .= ucfirst($param);
                            }

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
        }


//        var_dump($routes);

        return $routes;
    }

    /**
     * @param $folder
     * @return string
     */
    protected function trimFolder($folder)
    {
        return '/' . trim($folder, '/');
    }
}