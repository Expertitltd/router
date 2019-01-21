<?php

namespace Skalar\Type;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use Skalar\GraphQL\Types;

/**
 * Class QueryType
 * @package Skalar\Type
 */
class Query extends ObjectType
{
    /**
     * QueryType constructor.
     */
    public function __construct()
    {
        $config = [
            'fields' =>  function() {
                return [
                    'catalog' => [
                        'type' => Types::catalog(),
                        'description' => 'Catalog',
                        'resolve' => function () {
                            return [];
                        }
                    ],
                    'main' => [
                        'type' => Types::main(),
                        'description' => 'Main',
                        'resolve' => function () {
                            return [];
                        }
                    ],
                ];
            }
        ];
        parent::__construct($config);
    }
}