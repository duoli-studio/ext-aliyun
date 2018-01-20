<?php namespace System\Tests\Pam;

/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Support\Facades\Crypt;
use Poppy\Framework\Application\TestCase;
use System\Pam\Action\Pam;

class AccountTest extends TestCase
{
	public function testCrypt()
	{
		$string = Crypt::encryptString('okijmunhyhgytrgd');

		echo strlen($string);

		$string = 'eyJpdiI6IkEwSkI0Vzhic01LQWkraU1GeDl6K1E9PSIsInZhbHVlIjoieVNNS1d1QkQ4YnhuNFFVdUVOR3VNMWFURngzVjBsTmxNU0J6TUtaMlE5QT0iLCJtYWMiOiJhNzQ4MjMyM2U2NzlkZjVmMjliNDI4OGI2NDM5ZmNhZWNhOWUyNTgxOTFjZmRjY2UzMjdhZDAzNzc0MWRlYzkyIn0=';
		var_dump(Crypt::decryptString($string));
		dd($string);
	}

	public function testLogout()
	{
		/** @var Pam $actPam */
		$actPam = app('act.pam');
		if (!$actPam->webLogout()) {
			dd($actPam->getError());
		}
		else {
			dd($actPam->getSuccess());
		}
	}
}
