<?php
namespace Skalar;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class Request
 * @package Skalar
 */
class Request extends SymfonyRequest
{
    /**
     * @param $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}