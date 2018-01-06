<?php namespace Order\Game\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Order\Models\GameServer;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;


/**
 * Class SettingQuery.
 */
class UserServerQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'user_server';
		$this->attributes['description'] = trans('order::server.graphql.query_desc');
	}


	public function authorize($root, $args)
	{
		return $this->isJwtUser();
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('user_server');
	}

	/**
	 * @return array
	 */
	public function args()
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
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		return GameServer::find($args['id']);
	}
}
