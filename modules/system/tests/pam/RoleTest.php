<?php namespace System\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Models\Filters\RoleFilter;
use System\Models\PamAccount;
use System\Models\PamRole;
use System\Action\Role;

class RoleTest extends TestCase
{
	public function testCreate()
	{
		\Auth::guard(PamAccount::GUARD_BACKEND)->loginUsingId(1);
		$role = new Role();
		$item = $role->establish([
			'name'  => 'abc',
			'title' => 'abc',
			'type'  => 'backend',
		]);

		dd($role->getError());
	}

	public function testList()
	{
		$pageInfo = new PageInfo(['page' => 1]);
		$Db       = PamRole::filter([], RoleFilter::class);
		return PamRole::paginationInfo($Db, $pageInfo, function($item) {
			return $item;
		});
	}
}
