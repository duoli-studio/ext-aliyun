<?php namespace User\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use User\Pam\Action\Pam;

class PamTest extends TestCase
{
	/**
	 * @throws \Throwable
	 */
	public function testRegister()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');

		if ($pam->register('develop', 'develop')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

}