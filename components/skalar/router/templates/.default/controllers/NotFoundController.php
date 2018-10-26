<?php

use Symfony\Component\HttpFoundation\Request;

class NotFoundController
{
    public function index(Request $request, array $state){
        // do something
        return $state;
    }
}