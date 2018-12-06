<?php

namespace Skalar\Api;

use Skalar\Routing\AbstractApi;
use \Skalar\Controller\Api;

//use GraphQL\GraphQL as GQL;
//use GraphQL\Schema;
//use Skalar\Type\Types;

/**
 * Class GraphQL
 * @package Skalar\Api
 */
class GraphQL extends AbstractApi
{
    /**
     * @var string
     */
    protected $controllersFolder = 'graphql';
    protected $libFolder = "";

    public function __construct($templateFolder)
    {
        parent::__construct($templateFolder);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        $routes = [];

        $files = $this->getAllFolderFiles();
        foreach ($files as $file) {
            $filePath = $this->templateFolder . "/" . $this->controllersFolder . "/" . $file;
            if (file_exists($filePath)) {
                require_once($filePath);
            }
        }

        $path = '/' . $this->controllersFolder . '/';

        $defaults = [
            '_controller' => '\\Skalar\\GraphQL\\GraphQLController::execute',
        ];

        $routes[$this->controllersFolder] = $this->addRoute(
            $path,
            $defaults,
            "post"
        );

        return $routes;
    }

}