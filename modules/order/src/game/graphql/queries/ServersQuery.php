<?php namespace Order\game\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Order\Models\GameServer;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;


class ServersQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'servers';
		$this->attributes['description'] = trans('order::server.graphql.queries_desc');
	}


	public function authorize($root, $args)
	{
		// true, if logged in
		return !$this->getJwtBeGuard()->guest();
	}

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('server'));
	}

	/**
	 * @return array
	 */
	public function args()
	{
		return [];
	}

	/**
	 * @param $root
	 * @param $args
	 * @return mixed
	 */
	public function resolve($root, $args)
	{
		return GameServer::get();
	}


}
