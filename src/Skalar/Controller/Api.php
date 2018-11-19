<?php
namespace Skalar\Controller;

/**
 * Class Api
 * @package Skalar\Controller
 */
abstract class Api extends Base
{
    abstract function getRoutes();
}