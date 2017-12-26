<?php namespace System\Tests\Permission;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use System\Classes\Traits\SystemTrait;

class PermissionTest extends TestCase
{
	use SystemTrait;

	public function testPermission()
	{
		dd($this->getPermission()->repository());
	}

	public function testPermissions()
	{
		dd($this->getPermission()->permissions());
	}
}
