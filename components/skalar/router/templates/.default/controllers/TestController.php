<?php

use Symfony\Component\HttpFoundation\Request;

class TestController
{
    public function test(Request $request, array $state){
        $param = $request->get('param');
        // do something
        return $state;
    }
}