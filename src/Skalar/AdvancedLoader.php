<?php
namespace Skalar;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Skalar\Api\Router as ApiRouter;

/**
 * Class AdvancedLoader
 * @package Skalar
 */
class AdvancedLoader extends YamlFileLoader
{
    /**
     * @var array
     */
    private $apiFolder;

    /**
     * @var string
     */
    private $folder;

    /**
     * @var string
     */
    private $apiNamespace;

    /**
     * AdvancedLoader constructor.
     * @param FileLocatorInterface $locator
     * @param string $folder
     * @param string $apiFolder
     * @param string $apiNamespace
     */
    public function __construct(FileLocatorInterface $locator, $folder = '', $apiFolder = '', $apiNamespace = '')
    {
        $this->apiFolder = $this->trimFolder($apiFolder);
        $this->apiNamespace = '\\' . trim($apiNamespace, '\\') . '\\';
        $this->folder = $this->trimFolder($folder);
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
        $apiFiles = $this->getApiFiles();
        foreach ($apiFiles as $file) {
            $class = $this->apiNamespace . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($class)){
                $api = new $class($this->folder);
                if ($api instanceof ApiRouter) {
                    $api->setLoader();
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
     * @return array
     */
    protected function getApiFiles()
    {
        $dirPath = $this->folder . $this->apiFolder;
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