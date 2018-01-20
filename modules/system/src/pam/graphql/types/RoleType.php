<?php namespace System\Pam\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Poppy\Framework\GraphQL\Support\Type as AbstractType;

class RoleType extends AbstractType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'Role';
		$this->attributes['description'] = trans('system::role.graphql.type_desc');
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		return [
			'id'             => [
				'type'        => Type::int(),
				'description' => trans('system::role.db.id'),
			],
			'name'           => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.name'),
			],
			'title'          => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.title'),
			],
			'type'           => [
				'type'        => Type::string(),
				'description' => trans('system::role.db.type'),
			],
			'can_permission' => [
				'type'        => Type::boolean(),
				'description' => trans('system::role.graphql.can_permission'),
			],
		];
	}
}
