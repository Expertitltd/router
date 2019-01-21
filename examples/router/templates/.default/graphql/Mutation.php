<?php

namespace Skalar\Type;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use Skalar\GraphQL\Types;
use Skalar\Basket\BasketActions;
use Skalar\State;

class Mutation extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function() {
                return [
                    'addToBasket' => [
                        'type' => Types::basket(),
                        'args' => [
                            'items' => Type::nonNull(Type::listOf(Types::inputBasketItem())),
                            'section' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => function ($root, $args) {

                            // example!!!!!
                            $products = [];
                            foreach ($args['items'] as $item) {
                                $item['props'] = [
                                    ['NAME' => 'Section', 'CODE' => 'SECTION', 'VALUE' => $args['section'], 'SORT' => 500],
                                ];
                                $products[] = $item;
                            }

                            $basket = new BasketActions();
                            $basket->updateProductAdvanced($products);

                            return State::getBasketList($basket->getBasketList());
                        },
                    ],
                ];
            }
        ];
        parent::__construct($config);
    }
}