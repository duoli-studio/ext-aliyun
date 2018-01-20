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
		return Type::listOf($this->getGraphQL()->type('Role'));
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args()
	{
		return [
			'filters' => [
				'type'        => $this->getGraphQL()->type('RoleFilter'),
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
		$filters    = $args['filters'] ?? [];
		$collection = PamRole::filter($filters, RoleFilter::class)->get();
		$collection->map(function($item) {
			// root 用户不可以编辑权限
			$item->can_permission = $item->name != PamRole::BE_ROOT;
			return $item;
		});
		return $collection;
	}


}
