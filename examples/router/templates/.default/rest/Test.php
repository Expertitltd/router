<?php

namespace Skalar\Rest;

use Skalar\Routing\RestMethods;
use Symfony\Component\HttpFoundation\Request;
use Skalar\Controller\ApiController;

/**
 * Class Test
 */
class Test extends ApiController implements RestMethods
{
    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function get(Request $request, array $state)
    {
        // TODO: Implement get() method.
        $state[] = 'rest';
        $state['param1'] = $request->get('param1');
        $state['param2'] = $request->get('param2');
        return $state;
    }

    /**
     * @param Request $request
     * @param array $state
     * @return array
     */
    public function post(Request $request, array $state)
    {
        // TODO: Implement post() method.
        return $state;
    }
}