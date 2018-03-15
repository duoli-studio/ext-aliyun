<?php namespace System\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */
use Poppy\Framework\Application\TestCase;
use System\Action\Role;
use System\Models\Filters\RoleFilter;
use System\Models\PamAccount;
use System\Models\PamRole;

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
		$Db       = PamRole::filter([], RoleFilter::class);

		return PamRole::paginationInfo($Db, function ($item) {
			return $item;
		});
	}
}
