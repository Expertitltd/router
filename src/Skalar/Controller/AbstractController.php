<?php
namespace Skalar\Controller;

use \Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Skalar\Routing\Middleware;
use Skalar\Request;

/**
 * Class AbstractController
 * @package Skalar\Controller
 */
abstract class AbstractController
{
    /**
     * @var ResponseHeaderBag
     */
    protected $headers;

    /**
     * @var
     */
    protected $status;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Base constructor.
     */
    public function __construct()
    {
        $this->headers = new ResponseHeaderBag();
        $this->status = Response::HTTP_OK;
    }

    /**
     * @param array $state
     * @param $url
     * @return string
     */
    public function getRender()
    {
        return function (array $state, $url = '') {
            return json_encode($state);
        };
    }

    /**
     * @return ResponseHeaderBag
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $state
     * @param string $dir
     * @param Request|null $request
     * @return array
     */
    public function runMiddleware(array $state, $dir = '', Request $request = null)
    {
        if (empty($dir) || empty($request)) {
            return $state;
        }
        $middleware = new Middleware($dir);
        return $middleware->execute($request, $state);
    }
}