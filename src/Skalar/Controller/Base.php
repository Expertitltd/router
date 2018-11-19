<?php
namespace Skalar\Controller;

use \Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Base
 * @package Skalar\Controller
 */
abstract class Base
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
}