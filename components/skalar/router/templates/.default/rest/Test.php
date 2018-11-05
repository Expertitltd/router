<?php

use Symfony\Component\HttpFoundation\Request;

/**
 * Class Test
 */
class Test
{
    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function get(Request $request, array $state)
    {
        // do something
        return $state;
    }
}