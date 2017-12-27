<?php namespace User\Tests;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use User\Action\ActRegister;

class Registertest extends TestCase
{
	public function testRegister()
	{
		$register = new ActRegister();
		$register->register('cq','123456','123456','958693');
	}

	public function date()
	{
		\Log::debug(date('d'));
	}
}