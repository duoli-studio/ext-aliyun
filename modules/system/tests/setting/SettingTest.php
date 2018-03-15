<?php namespace System\Tests\Setting;

/**
 * Copyright (C) Update For IDE
 */
use Poppy\Framework\Application\TestCase;
use System\Classes\Traits\SystemTrait;

class SettingTest extends TestCase
{
	use SystemTrait;

	public function testCreate()
	{
		$this->getSetting()->getNsGroup('system', 'site');
	}
}
