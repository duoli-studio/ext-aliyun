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
	/**
	 * @throws \Throwable
	 */
	public function testRegister()
	{
		/** @var Pam $pam */
		/** @var User $user */
		/** @var Fans $pam */
		$pam  = app('act.pam');
		$user = app('act.user');
		if ($pam->register(18654958691, 123456, '熊猫', 1)) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

	public function testCaptcha()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->RecoverPassword(18654958691)) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

	public function testValidate()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->validateCaptcha(18654958691,487853)) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

	public function testSendValidate()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->captchaRegister(18864838035)) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

	public function testPwdLogin()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->loginPwd(18654958691,123456)) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}
	public function testUpdatePwd()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->findPassword(18654958691,'123457')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

	public function testChangePwd()
	{
		/** @var Fans $pam */
		$pam  = app('act.pam');
		$user = PamAccount::where('username', 'imvkmark')->first();
		if ($pam->setPassword($user, '123456')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

}