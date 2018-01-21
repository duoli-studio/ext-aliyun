<?php namespace System\Pam\Graphql\Input;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;


class BeRoleFilterType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'BeRoleFilter';
		$this->attributes['description'] = trans('system::role.graphql.input_role_filter_type');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'type' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::role.db.type'),
			],
			'id'   => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.id'),
			],
		];
	}
}
