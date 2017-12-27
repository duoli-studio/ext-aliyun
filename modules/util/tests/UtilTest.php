<?php namespace Util\Tests;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Util\Action\ActUtil;
class UtilTest extends TestCase
{
    //
	public function testUtil(){
		$util = new ActUtil();
		$util->getCaptcha('10','123456789@qq.com','');
	}
}
