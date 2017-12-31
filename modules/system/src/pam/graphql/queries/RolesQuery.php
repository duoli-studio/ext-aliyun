<?php namespace System\Pam\Graphql\Queries;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\RoleFilter;
use System\Models\PamRole;


/**
 * Class SettingQuery.
 */
class RolesQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'roles';
		$this->attributes['description'] = trans('system::role.graphql.queries_desc');
	}

	/**
	 * @return ListOfType
	 * @throws TypeNotFound
	 */
	public function type(): ListOfType
	{
		return Type::listOf($this->getGraphQL()->type('role'));
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args()
	{
		return [
			'filters' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('role_filter')),
				'description' => trans('system::role.graphql.filter_desc'),
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
		return PamRole::filter($args['filters'], RoleFilter::class)->get();
	}


}
