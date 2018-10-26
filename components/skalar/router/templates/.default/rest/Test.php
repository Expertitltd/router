<?php

use Symfony\Component\HttpFoundation\Request;

class Test
{
    public function get(Request $request, array $state){
        // do something
        return $state;
    }
}