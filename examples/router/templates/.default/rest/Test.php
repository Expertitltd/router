<?php

use Skalar\RestMethods;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Test
 */
class Test implements RestMethods
{
    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function get(Request $request, array $state)
    {
        // TODO: Implement get() method.
        return $state;
    }

    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function post(Request $request, array $state)
    {
        // TODO: Implement post() method.
        return $state;
    }
}