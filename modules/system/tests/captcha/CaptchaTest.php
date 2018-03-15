<?php namespace System\Tests;

/**
 * Copyright (C) Update For IDE
 */
use Poppy\Framework\Application\TestCase;
use System\Action\Verification;
use System\Models\PamCaptcha;

class CaptchaTest extends TestCase
{
	/**
	 * @throws \Illuminate\Container\EntryNotFoundException
	 */
	public function testSend()
	{
		$actCaptcha = new Verification();
		$mobile     = '1388888' . rand(1111, 9999);
		if (!$actCaptcha->send($mobile, PamCaptcha::CON_LOGIN)) {
			dd($actCaptcha->getError());
		}
		else {
			$item = PamCaptcha::where('passport', $mobile)->first()->toArray();
			dd($item);
			dd($actCaptcha->getSuccess());
		}
	}
}
