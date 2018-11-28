<?php

namespace Skalar\Routing;

use Symfony\Component\Routing\Route;


/**
 * Class AbstractApi
 * @package Skalar\Routing
 */
abstract class AbstractApi
{

    protected $controllersFolder = "";
    protected $templateFolder = "";

    /**
     * AbstractApi constructor.
     * @param $templateFolder
     * @param string $apiFolder
     */
    public function __construct($templateFolder)
    {
        $this->setTemplateFolder($templateFolder);
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
     * @param $folder
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
}