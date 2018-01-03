<?php namespace User\Fans\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use User\Models\UserFans;


class FansQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'fans';
		$this->attributes['description'] = trans('user::fans.graphql.queries_desc');
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
		return Type::listOf($this->getGraphQL()->type('fans'));
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
		return UserFans::get();
	}


}
