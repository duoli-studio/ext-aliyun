<?php namespace User\Pam\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use User\Models\UserProfile;


class ChangeQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'change';
		$this->attributes['description'] = trans('user::change.graphql.queries_desc');
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
		return Type::listOf($this->getGraphQL()->type('change'));
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
		return UserProfile::get();
	}


}
