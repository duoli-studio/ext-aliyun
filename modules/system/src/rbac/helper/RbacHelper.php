<?php namespace System\Rbac\Helper;

use Illuminate\Database\Eloquent\Collection;
use System\Models\PamPermission;

/**
 * Class RbacHelper
 * @package Sour\System\Rbac\Helper
 */
class RbacHelper
{
	/**
	 * 获取权限以及分组
	 * @param $account_type
	 * @return Collection
	 */
	public static function permission($account_type)
	{
		$permission = PamPermission::where('account_type', $account_type)->get();
		$collection = new Collection($permission);

		return $collection->groupBy('permission_group');
	}
}

