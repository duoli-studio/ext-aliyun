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

	public function testCaptchaLogin()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->captchaLogin(18654958691)) {
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
		if ($pam->captchaLogin(18654958691, '042512')) {
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
		if ($pam->loginPwd(18654958691, 123457)) {
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
		if ($pam->findPassword(18654958691, '123456')) {
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

	public function testResetPwd()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->resetPassword(107, '19970302', '4515222')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		};
	}

}