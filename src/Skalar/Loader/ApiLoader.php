<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27.11.18
 * Time: 15:22
 */

namespace Skalar\Loader;

use Skalar\Routing\AbstractApi;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;


/**
 * Class ApiLoader
 * @package Skalar\Loader
 */
class ApiLoader extends YamlFileLoader
{
    /**
     * @var array
     */
    private $apiFolder;

    /**
     * @var string
     */
    private $folders;

    /**
     * @var
     */
    private $templateFolder;

    /**
     * @var string
     */
    private $apiNamespace = '\\Skalar\\Api\\';
    /**
     * @var string
     */
    private $srcApiFolder = __DIR__ . '/../Api';

    /**
     * ApiLoader constructor.
     * @param array $folders
     * @param string $templateFolder
     */
    public function __construct($templateFolder = "", $folders = [])
    {
        $locator = new FileLocator([__DIR__]);

        $this->folders = $folders;
        $this->templateFolder = $templateFolder;

        parent::__construct($locator);
    }

    /**
     * @param string $resource
     * @param null $type
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        $routes = [];
        $apiFiles = array_merge(
            $this->getFiles($this->templateFolder . "/api"),
            $this->getFiles($this->srcApiFolder)

        );
        foreach ($this->folders as $folder) {
            $apiFiles = array_merge($apiFiles, $this->getFiles($folder));
        }

        foreach ($apiFiles as $file) {
            $class = $this->apiNamespace . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($class)){
                $api = new $class($this->templateFolder);

                if ($api instanceof AbstractApi) {
                    $routes = array_merge($routes, $api->getRoutes());
                }
            }
        }

        foreach ($routes as $routeName => $route) {
            $collection->add($routeName, $route);
        }

        $collection->addCollection(parent::load($resource, $type));

        return $collection;
    }

    /**
     * @param $dirPath
     * @return array
     */
    protected function getFiles($dirPath)
    {
        $fileList = scandir($dirPath);

        $fileList = array_filter($fileList, function ($val) {
            return pathinfo($val,PATHINFO_EXTENSION) == 'php';
        });

        foreach ($fileList as $file) {
            $fullFilePath = $this->folder . $this->apiFolder . $this->trimFolder($file);
            if (file_exists($fullFilePath)) {
                require_once $fullFilePath;
            }
        }

        return $fileList;
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