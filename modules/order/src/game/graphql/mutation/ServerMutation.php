<?php namespace Order\Game\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class ServerMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'server';
		$this->attributes['description'] = trans('order::server.graphql.mutation_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('resp');
	}

	/**
	 * @return array
	 */
	public function args(): array
	{
		return [
			'title'     => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('order::server.db.title'),
			],
			'parent_id' => [
				'type'        => Type::int(),
				'description' => trans('order::server.db.parent_id'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return array
	 */
	public function resolve($root, $args)
	{
		$id     = $args['parent_id'] ?? 0;
		$server = app('act.server');
		if (!$server->establish($args, $id)) {
			return $server->getError()->toArray();
		}
		else {
			return $server->getSuccess()->toArray();
		}
	}
}