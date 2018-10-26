<?php

use Symfony\Component\HttpFoundation\Request;

class ExampleMiddleware
{

    public function __invoke(Request $request, array $state)
    {
        // do something
        return $state;
    }
}