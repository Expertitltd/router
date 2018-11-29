<?php

namespace Skalar\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RestMethods
 * @package Skalar\Rest
 */
interface RestMethods
{
    /**
     * @param Request $request
     * @param array $state
     * @return mixed
     */
    public function get(Request $request, array $state);

    /**
     * @param Request $request
     * @param array $state
     * @return mixed
     */
    public function post(Request $request, array $state);
}