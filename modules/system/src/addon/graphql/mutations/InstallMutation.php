<?php
namespace System\Addon\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Abstracts\Mutation;

/**
 * Class ConfigurationMutation.
 */
class InstallMutation extends Mutation
{
    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'identification' => [
                'name' => 'identification',
                'type' => Type::string(),
            ],
        ];
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return '';
    }

    /**
     * @param $root
     * @param $args
     *
     * @return mixed|void
     */
    public function resolve($root, $args)
    {
        // TODO: Implement resolve() method.
    }
}
