<?php namespace User\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use System\Models\PamAccount;
use User\Pam\Action\Fans;

class PamTest extends TestCase
{
	/**
	 * @throws \Throwable
	 */
	public function testRegister()
	{
		/** @var Fans $pam */
		$pam = app('act.pam');
		if ($pam->register('develop', 'develop')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

	public function testChangePwd()
	{
		/** @var Fans $pam */
		$pam = app('act.pam');
		$user = PamAccount::where('username', 'imvkmark')->first();
		if ($pam->setPassword($user, '123456')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

}