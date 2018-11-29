<?php
namespace Skalar\Controller;

use Skalar\Request;

/**
 * Class ApiController
 * @package Skalar\Controller
 */
abstract class ApiController extends AbstractController
{
    /**
     * @param array $state
     * @param string $dir
     * @param Request|null $request
     * @return array
     */
    public function runMiddleware(array $state, $dir = '', Request $request = null)
    {
        return parent::runMiddleware($state);
    }
}