<?php

namespace Skalar\Rest;

use Skalar\Api\Router;
use Symfony\Component\Routing\Route;

/**
 * Class RestRouter
 * @package Skalar\Rest
 */
class RestRouter extends Router
{
    /**
     * @var string
     */
    private $controllersFolder = 'rest/controllers';
    private $params = [
        '',
        'param1',
        'param2',
    ];

    /**
     *
     */
    public function setLoader()
    {
        $folder = $this->trimFolder($this->controllersFolder);
        spl_autoload_register(function($class) use ($folder)
        {
            $arClass = explode('\\', trim($class, '\\'));
            $class = end($arClass);
            $file = $this->templateFolder . $folder . '/' . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        $routes = [];
        $files = $this->getAllFolderFiles($this->controllersFolder);
        foreach ($files as $file) {
            $reflectionClass = new \ReflectionClass('\\' . ltrim(str_replace('.php', '', $file), '\\'));
            $className = $reflectionClass->getName();
            $methods = $reflectionClass->getMethods();
            if (!empty($methods) && is_array($methods)) {
                foreach($methods as $method) {
                    if ($method->getDeclaringClass()->getName() == $className) {
                        $path = '/rest/' . strtolower($className) . '/';
                        $routeName = $method->name . $className;
                        foreach ($this->params as $param) {
                            if (!empty($param)) {
                                $path .= '{' . $param . '}/';
                                $routeName .= ucfirst($param);
                            }
                            $defaults = [
                                '_controller' => $className . '::' . $method->name,
                            ];
                            $route = new Route($path, $defaults);
                            $route->setMethods([$method->name]);
                            $routes[$routeName] = $route;
                        }
                    }
                }
            }
        }
        return $routes;
    }

    /**
     * @param $folder
     * @return array
     */
    protected function getAllFolderFiles($folder)
    {
        $dirPath = $this->templateFolder . $this->trimFolder($folder);
        $fileList = scandir($dirPath);
        $fileList = array_diff($fileList, ['.', '..']);
        return array_values($fileList);
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