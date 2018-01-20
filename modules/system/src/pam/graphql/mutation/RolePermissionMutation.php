<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\Role;

class RolePermissionMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'role_permission';
		$this->attributes['description'] = trans('system::role.graphql.mutation_permission');
	}

	/**
	 * @return ObjectType
	 * @throws TypeNotFound
	 */
	public function type(): ObjectType
	{
		return $this->getGraphQL()->type('Resp');
	}

	/**
	 * @return array
	 */
	public function args(): array
	{
		return [
			'permission' => [
				'type'        => Type::nonNull(Type::listOf(Type::int())),
				'description' => trans('system::role.graphql.mutation_permission_arg_permission'),
			],
			'role_id'    => [
				'type'        => Type::nonNull(Type::int()),
				'description' => trans('system::role.db.role_id'),
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
		$permission = $args['permission'] ?? null;
		$role_id    = $args['role_id'] ?? null;

		/** @var Role $role */
		$role = app('act.role')->setPam($this->getJwtBeGuard()->user());
		if (!$role->savePermission($role_id, $permission)) {
			return $role->getError()->toArray();
		} else {
			return $role->getSuccess()->toArray();
		}
	}
}