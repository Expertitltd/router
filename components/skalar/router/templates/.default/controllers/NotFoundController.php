<?php

use Symfony\Component\HttpFoundation\Request;

/**
 * Class NotFoundController
 */
class NotFoundController
{
    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function index(Request $request, array $state)
    {
        // do something
        return $state;
    }
}