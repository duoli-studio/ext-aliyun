<?php namespace User\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use System\Models\PamAccount;
use User\Pam\Action\Pam;
use User\Pam\Action\User;
use User\Pam\Action\Fans;

class PamTest extends TestCase
{
	public function testPam()
	{
		$pam = app('act.pam');
		dd($pam);
	}

	public function testRegisterEmptyPassword()
	{
		$mobile = '152' . rand('1111', '9999') . '9938';

		/** @var Pam $pam */
		$pam    = app('act.pam');
		$result = $pam->register($mobile);
		dd($result);
	}

	public function testValidate()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->captchaLogin(18654958691, '042512')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}

	}


	public function testUpdatePwd()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->updatePassword(18654958691, '132213', '19970302')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

	public function testChangePwd()
	{
		/** @var Pam $pam */
		$pam  = app('act.pam');
		$user = PamAccount::where('username', 'imvkmark')->first();
		if ($pam->setPassword($user, '123456')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

	public function testLoginCheck()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->loginCheck('18654958691', '123456')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

}