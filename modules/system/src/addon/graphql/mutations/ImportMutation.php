<?php namespace System\Addon\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Mutation;

/**
 * Class ImportMutation.
 */
class ImportMutation extends Mutation
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
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		return [];
	}
}
