<?php namespace System\Pam\Graphql\Input;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;


class InputRoleType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'InputRole';
		$this->attributes['description'] = trans('system::role.graphql.input_role_type');
	}

	/**
	 * @return array
	 * @throws \Poppy\Framework\GraphQL\Exception\TypeNotFound
	 */
	public function fields()
	{
		return [
			'name'        => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.name'),
			],
			'title'       => [
				'description' => trans('system::role.db.title'),
				'type'        => Type::string(),
			],
			'guard'       => [
				'description' => trans('system::role.db.type'),
				'type'        => Type::nonNull(app('graphql')->type('RoleGuard')),
			],
			'description' => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.description'),
			],
		];
	}
}
