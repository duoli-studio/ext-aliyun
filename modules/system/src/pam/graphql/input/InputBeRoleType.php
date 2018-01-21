<?php namespace System\Pam\Graphql\Input;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;


class InputBeRoleType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'InputBeRole';
		$this->attributes['description'] = trans('system::role.graphql.input_role_type');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'name'        => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.name'),
			],
			'title'       => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.title'),
			],
			'guard'       => [
				'type'        => app('graphql')->type('BeRoleGuard'),
				'description' => trans('system::role.db.type'),
			],
			'description' => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.description'),
			],
		];
	}
}
