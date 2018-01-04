<?php namespace User\Tests\Fans;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use System\Classes\Traits\SystemTrait;
use User\Fans\Action\Fans;

class FansTest extends TestCase
{
	// use SystemTrait;
	/**
	 * @throws \Throwable
	 */
	public function testConcern()
	{
		/** @var Fans $fans */
		$fans = app('act.fans');
		if ($fans->concern(14)) {
			dd($fans->getSuccess());
		}
		else {
			dd($fans->getError());
		}
	}


}