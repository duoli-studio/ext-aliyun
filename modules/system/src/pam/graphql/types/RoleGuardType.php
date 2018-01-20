<?php namespace System\Pam\GraphQL\Types;

use Poppy\Framework\GraphQL\Support\EnumType;
use System\Models\PamAccount;

/**
 * Class SettingType.
 */
class RoleGuardType extends EnumType
{

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);
		$this->attributes['name']        = 'RoleGuard';
		$this->attributes['description'] = trans('system::role.graphql.type_desc');
		$this->attributes['values']      = [
			PamAccount::GUARD_BACKEND => [
				'description' => '后台角色',
			],
			PamAccount::GUARD_WEB     => [
				'description' => '用户角色',
			],
			PamAccount::GUARD_DEVELOP => [
				'description' => '开发者角色',
			],
		];
	}
}
