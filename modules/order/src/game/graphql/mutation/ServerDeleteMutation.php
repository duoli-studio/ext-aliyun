<?php namespace Order\Game\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;

class ServerDeleteMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'server_delete';
		$this->attributes['description'] = trans('order::server.graphql.delete_desc');
	}

	public function authorize($root, $args)
	{
		return $this->isJwtBackend();
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
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('order::server.db.id'),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return array
	 * @throws \Exception
	 */
	public function resolve($root, $args)
	{
		// todo
		$id     = $args['id'] ?? 0;
		$server = app('act.server');
		if (!$server->setPam($this->getJwtBeGuard()->user())->delete($id)) {
			return $server->getError()->toArray();
		}
		else {
			return $server->getSuccess()->toArray();
		}
	}
}