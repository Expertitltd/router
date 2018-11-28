<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27.11.18
 * Time: 14:54
 */

namespace Skalar\Api;

use Skalar\Routing\AbstractApi;
use \Skalar\Controller\Api;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class GraphQL
 * @package Skalar\Api
 */
class GraphQL extends AbstractApi
{
    protected $controllersFolder = 'graphql';

    public function getRoutes()
    {
        $routes = [];
        $files = $this->getAllFolderFiles();

        require_once($this->templateFolder . "/" . $this->controllersFolder . "/GraphQL.php");

        $path = '/' . $this->controllersFolder . '/';

        $defaults = [
            '_controller' => '\Skalar\GraphQL\GraphQL::execute',
        ];

        $routes[$this->controllersFolder] = $this->addRoute(
            $path,
            $defaults,
            "post"
        );

//        var_dump($routes);

        return $routes;
    }
}