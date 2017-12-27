<?php namespace User\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use User\Pam\Action\Pam;

class PamTest extends TestCase
{
	public function testRegister()
	{
		/** @var Pam $pam */
		$pam = app('act.pam');
		if ($pam->register('19954958694', '()*kshkfskfhk')) {
			dd($pam->getSuccess());
		}
		else {
			dd($pam->getError());
		}
	}

}