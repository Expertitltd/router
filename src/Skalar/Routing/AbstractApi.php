<?php

namespace Skalar\Routing;

use Symfony\Component\Routing\Route;


/**
 * Class AbstractApi
 * @package Skalar\Routing
 */
abstract class AbstractApi
{

    /**
     * @var string
     */
    protected $controllersFolder = "";
    /**
     * @var string
     */
    protected $templateFolder = "";
    /**
     * @var string
     */
    protected $libFolder = "lib";
    /**
     * @var string
     */
    protected $namespace = "Skalar";

    /**
     * AbstractApi constructor.
     * @param $templateFolder
     */
    public function __construct($templateFolder)
    {
        $this->setTemplateFolder($templateFolder);
        $this->setLoader($this->libFolder);
    }

    /**
     * @param $templateFolder\
     */
    public function setTemplateFolder($templateFolder)
    {
        $this->templateFolder = $templateFolder;
    }

    /**
     * @param $path
     * @param $defaults
     * @param $method
     * @return Route
     */
    protected function addRoute($path, $defaults, $method)
    {
        $route = new Route($path, $defaults);

        $route->setMethods([
            $method
        ]);

        return $route;
    }

    /**
     * @return array
     */
    protected function getAllFolderFiles()
    {
        $fileList = scandir($this->templateFolder . "/" . $this->controllersFolder);
        $fileList = array_diff($fileList, ['.', '..']);
        return array_values($fileList);
    }

    /**
     * @return mixed
     */
    abstract public function getRoutes();

    /**
     * @param string $folder
     */
    protected function setLoader($folder = '')
    {
        $path = $this->templateFolder . "/" . $this->controllersFolder . "/". trim($folder, '/');
        spl_autoload_register(function($class) use ($path)
        {
            $classPath = str_replace('\\', '/', trim(str_replace($this->namespace . '\\', '', $class), '\\'));
            $file = $path . '/' . $classPath . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}