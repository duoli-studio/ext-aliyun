<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\Role;

class RoleDoMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'role_do';
		$this->attributes['description'] = trans('system::role.graphql.do_desc');
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
		return $this->getGraphQL()->type('Resp');
	}

	/**
	 * @return array
	 * @throws TypeNotFound
	 */
	public function args(): array
	{
		return [
			'action' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('RoleDoAction')),
				'description' => trans('system::role.db.title'),
			],
			'id'     => [
				'type'        => Type::int(),
				'description' => trans('system::role.db.id'),
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
		$id = $args['id'] ?? null;

		/** @var Role $role */
		$role = app('act.role');
		if (!$role->setPam($this->getJwtBeGuard()->user())->delete($id)) {
			return $role->getError()->toArray();
		}
		else {
			return $role->getSuccess()->toArray();
		}
	}
}