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

	public function authorize($root, $args)
	{
		// true, if logged in
		return !$this->getJwtBeGuard()->guest();
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
			'id' => [
				'type'        => Type::int(),
				'description' => trans('order::server.db.id'),
			],
			'title'     => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('order::server.db.title'),
			],
			'parent_id' => [
				'type'        => Type::int(),
				'description' => trans('order::server.db.parent_id'),
			],
			'is_enable' => [
				'type'        => Type::int(),
				'description' => trans('order::server.db.is_enable'),
			],
			'is_default' => [
				'type'        => Type::int(),
				'description' => trans('order::server.db.is_default'),
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
		$id     = $args['id'] ?? 0;
		$server = app('act.server');
		$server->setPam($this->getJwtBeGuard()->user());
		if (!$server->establish($args, $id)) {
			return $server->getError()->toArray();
		}
		else {
			return $server->getSuccess()->toArray();
		}
	}
}