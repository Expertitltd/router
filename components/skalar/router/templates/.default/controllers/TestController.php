<?php

use Symfony\Component\HttpFoundation\Request;

/**
 * Class TestController
 */
class TestController
{
    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function test(Request $request, array $state)
    {
        $param = $request->get('param');
        // do something
        return $state;
    }
}