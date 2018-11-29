<?php

use Skalar\Controller\PublicController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TestController
 */
class TestController extends PublicController
{
    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function test(Request $request, array $state)
    {
        $state[] = 'TestController::test';
        // do something
        return $state;
    }
}