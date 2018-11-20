<?php
namespace Skalar\Api;

/**
 * Class Router
 * @package Skalar\Api
 */
abstract class Router
{
    /**
     * @var
     */
    protected $apiFolder;
    /**
     * @var
     */
    protected $templateFolder;

    /**
     * Router constructor.
     * @param $templateFolder
     * @param string $apiFolder
     */
    public function __construct($templateFolder, $apiFolder = '/api')
    {
        $this->setTemplateFolder($templateFolder);
        $this->setApiFolder($this->templateFolder . $apiFolder);
    }

    /**
     * @return mixed
     */
    public function getTemplateFolder()
    {
        return $this->templateFolder;
    }

    /**
     * @param mixed $templateFolder
     */
    public function setTemplateFolder($templateFolder)
    {
        $this->templateFolder = $templateFolder;
    }

    /**
     * @return mixed
     */
    public function getApiFolder()
    {
        return $this->apiFolder;
    }

    /**
     * @param mixed $apiFolder
     */
    public function setApiFolder($apiFolder)
    {
        $this->apiFolder = '/' . trim($apiFolder, '/');
    }

    /**
     * @return mixed
     */
    abstract function setLoader();

    /**
     * @return mixed
     */
    abstract function getRoutes();
}