<?php

namespace Skalar\Api;

use Skalar\Routing\AbstractApi;
use \Skalar\Controller\Api;


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

    /**
     * @return array
     */
    public function getRoutes()
    {
        $routes = [];

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

        return $routes;
    }
}