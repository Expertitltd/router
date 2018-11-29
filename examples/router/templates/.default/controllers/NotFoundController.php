<?php

use Skalar\Controller\PublicController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NotFoundController
 */
class NotFoundController extends PublicController
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