<?php namespace System\Pam\GraphQL\Types;

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
				'description' => trans('system::role.db.name'),
			],
			'title' => [
				'description' => trans('system::role.db.title'),
				'type'        => Type::string(),
			],
			'type'  => [
				'description' => trans('system::role.db.type'),
				'type'        => Type::string(),
			],
		];
	}
}
