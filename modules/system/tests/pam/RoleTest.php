<?php namespace System\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use System\Models\PamAccount;

class RoleTest extends TestCase
{
	public function testCreate()
	{
		\Auth::guard(PamAccount::GUARD_BACKEND)->loginUsingId(1);
		$role = app('act.role');
		$item = $role->establish([
			'name'  => 'abc',
			'title' => 'abc',
			'type' => 'backend',
		]);

		dd($role->getError());
	}
}
