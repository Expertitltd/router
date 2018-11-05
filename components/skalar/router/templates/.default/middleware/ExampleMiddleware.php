<?php

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExampleMiddleware
 */
class ExampleMiddleware
{

    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function __invoke(Request $request, array $state)
    {
        // do something
        return $state;
    }
}