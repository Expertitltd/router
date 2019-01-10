<?php

namespace Skalar\GraphQL;

use Symfony\Component\HttpFoundation\Request;
use Skalar\Controller\ApiController;

use GraphQL\GraphQL as GQL;
use GraphQL\Schema;
use Skalar\GraphQL\Types;


/**
 * Class GraphQLController
 * @package Skalar\Controller
 */
class GraphQLController extends ApiController
{

    /**
     * @param Request $request
     * @param array $state
     * @return array|\GraphQL\Executor\Promise\Promise
     */
    public function execute(Request $request, array $state)
    {
        try {
            list($query, $variableValues, $operationName) = $this->getQuery();

            $schema = $this->getSchema();

            $result = GQL::execute($schema, $query, null, null, $variableValues, $operationName);
        } catch (\Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getQuery()
    {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        $query = $input['query'];
        $variableValues = isset($input['variables']) ? $input['variables'] : null;
        $operationName = isset($input['operationName']) ? $input['operationName'] : null;

        return [
            $query,
            $variableValues,
            $operationName,
        ];
    }

    /**
     * @return Schema
     */
    protected function getSchema()
    {
        return new Schema([
            'query' => Types::query(),
            'mutation' => Types::mutation(),
        ]);
    }
}