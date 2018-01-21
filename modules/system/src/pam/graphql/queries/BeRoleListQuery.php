<?php namespace System\Pam\Graphql\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Query;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\RoleFilter;
use System\Models\PamRole;
use System\Models\Resources\Role;


class BeRoleListQuery extends Query
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_role_list';
		$this->attributes['description'] = '[O]' . trans('system::role.graphql.queries_desc');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('BeRoleList');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args()
	{
		return [
			'filters'    => [
				'type'        => $this->getGraphQL()->type('BeRoleFilter'),
				'description' => trans('system::role.graphql.filter_desc'),
			],
			'pagination' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('InputPagination')),
				'description' => trans('system::role.graphql.pagination'),
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
		$filters  = $args['filters'] ?? [];
		$pageInfo = new PageInfo($args['pagination']);
		$Db       = PamRole::filter($filters, RoleFilter::class);
		return PamRole::paginationInfo($Db, $pageInfo, Role::class);
	}
}
