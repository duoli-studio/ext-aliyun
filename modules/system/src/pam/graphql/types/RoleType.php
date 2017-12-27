<?php namespace System\Setting\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

/**
 * Class SettingType.
 */
class RoleType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'role';
		$this->attributes['description'] = trans('system::role.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'name'  => [
				'type'        => Type::string(),
				'description' => trans('system::db.role.name'),
			],
			'title' => [
				'description' => trans('system::db.role.title'),
				'type'        => Type::string(),
			],
			'type'  => [
				'description' => trans('system::db.role.type'),
				'type'        => Type::string(),
			],
		];
	}
}
