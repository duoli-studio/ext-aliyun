<?php namespace System\Pam\Graphql\Input;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\InputType;

/**
 * Class SettingType.
 */
class RoleFilterType extends InputType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'role_filter';
		$this->attributes['description'] = trans('system::role.graphql.filter_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'type' => [
				'type'        => Type::nonNull(Type::string()),
				'description' => trans('system::db.role.type'),
			],
			'id'   => [
				'type'        => Type::string(),
				'description' => trans('system::db.role.id'),
			],
		];
	}
}
