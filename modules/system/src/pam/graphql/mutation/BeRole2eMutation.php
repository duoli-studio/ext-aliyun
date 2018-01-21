<?php namespace System\Pam\GraphQL\Mutation;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Exception\TypeNotFound;
use Poppy\Framework\GraphQL\Support\Mutation;
use System\Classes\Traits\SystemTrait;
use System\Pam\Action\Role;

class BeRole2eMutation extends Mutation
{
	use SystemTrait;


	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'be_role_2e';
		$this->attributes['description'] = '[O]' . trans('system::role.graphql.mutation_desc');
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
			'item' => [
				'type'        => Type::nonNull($this->getGraphQL()->type('InputBeRole')),
				'description' => trans('system::role.db.title'),
			],
			'id'   => [
				'type'        => Type::int(),
				'description' => trans('system::role.db.id'),
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
		$role = app('act.role')->setPam($this->getJwtBeGuard()->user());
		if (!$role->establish($args['item'], $id)) {
			return $role->getError()->toArray();
		}
		else {
			return $role->getSuccess()->toArray();
		}
	}
}