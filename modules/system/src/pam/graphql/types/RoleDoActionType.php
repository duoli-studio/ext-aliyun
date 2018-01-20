<?php namespace System\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;

class RoleDoActionType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'RoleDoAction';
		$this->attributes['description'] = trans('system::role.graphql.type_desc');
		$this->attributes['values']      = [
			'delete' => [
				'description' => '删除角色',
			],
		];
	}
}
