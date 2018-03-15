<?php namespace System\Tests\Permission;

/**
 * Copyright (C) Update For IDE
 */
use Poppy\Framework\Application\TestCase;
use System\Classes\Traits\SystemTrait;
use System\Models\PamPermission;
use System\Models\PamRole;

class PermissionTest extends TestCase
{
	use SystemTrait;

	public function testPermission()
	{
		dd($this->getPermission()->repository());
	}

	public function testPermissions()
	{
		$permissions = $this->getPermission()->permissions();

		$role = PamRole::find(5);
		$type = $role->type;

		$keys              = $permissions->keys();
		$match             = PamPermission::where('type', $type)->whereIn('name', $keys)->pluck('id', 'name');
		$collectPermission = collect();
		foreach ($permissions as $key => $permission) {
			$tmp       = $permission->toArray();
			$tmp['id'] = $match->get($tmp['key']);
			$collectPermission->put($key, $tmp);
		}

		$permission = [];
		$collectPermission->each(function ($item, $key) use (&$permission, $role) {
			$root    = [
				'title'  => $item['root_title'],
				'root'   => $item['root'],
				'groups' => [],
			];
			$rootKey = $item['root'];
			if (!isset($permission[$rootKey])) {
				$permission[$rootKey] = $root;
			}
			$groupKey = $item['group'];
			$group    = [
				'group'       => $item['group'],
				'title'       => $item['group_title'],
				'permissions' => [],
			];
			if (!isset($permission[$rootKey]['groups'][$groupKey])) {
				$permission[$rootKey]['groups'][$groupKey] = $group;
			}

			$item['value'] = intval($role->hasPermission($key));

			unset(
				$item['is_default'],
				$item['root'],
				$item['group'],
				$item['module'],
				$item['key'],
				$item['root_title'],
				$item['type'],
				$item['group_title']
			);

			$permission[$rootKey]['groups'][$groupKey]['permissions'][] = $item;
		});

		dd($permission);
	}
}
