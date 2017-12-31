<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\Role;

class RoleMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'role';
		$this->attributes['description'] = trans('system::role.graphql.mutation_desc');
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
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'id'          => [
				'type'        => Type::int(),
				'description' => trans('system::role.db.id'),
			],
			'title'       => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::role.db.title'),
			],
			'type'        => [
				'type'        => Type::nonNull($this->getGraphQL()->type('role_guard')),
				'description' => trans('system::role.db.type'),
			],
			'description' => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.description'),
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
		$id = $args['id'] ?? null;

		/** @var Role $role */
		$role = app('act.role');
		if (!$role->setPam($this->getJwtBeGuard()->user())->establish($args, $id)) {
			return $role->getError()->toArray();
		}
		else {
			return $role->getSuccess()->toArray();
		}
	}
}