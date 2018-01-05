<?php namespace User\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use User\Pam\Action\Pam;
use User\Pam\Action\User;

class UserTest extends TestCase
{
	public function testUser()
	{
		$pam = app('act.pam');
		dd($pam);
	}

	/**
	 * @throws \Throwable
	 */
	public function testRegister()
	{
		/** @var Pam $actPam */
		$actPam = app('act.pam');

		/** @var User $actUser */
		$actUser = app('act.user');
		$mobile  = '152' . rand('1111', '9999') . '9938';
		if ($actPam->register($mobile)) {
			$actUser->setPam($actPam->getPam());
			if (!$actUser->register('test', 0, '123456')) {
				dd($actUser->getError());
			}
			else {
				dd($actUser->getSuccess());
			}
		}
		else {
			dd($actPam->getError());
		}
	}

}