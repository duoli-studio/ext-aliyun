<?php namespace System\Tests\Module;


use Poppy\Framework\Application\TestCase;
use System\Classes\Traits\SystemTrait;
use System\Setting\Events\SettingUpdated;

class ModuleManagerTest extends TestCase
{
	use SystemTrait;


	public function testRepository()
	{
		// dd(get_included_files());
		dd(app('module')->repository());
	}

	public function testMenu()
	{
		// dd(get_included_files());
		dd(app('module')->menus());
	}

	public function testAssets()
	{
		// dd(get_included_files());
		dd(app('module')->assets());
	}

	public function testPages()
	{
		// dd(get_included_files());
		event(new SettingUpdated());
		dd(app('module')->pages());
	}

	public function testSettings()
	{
		// dd(get_included_files());
		dd(app('module')->settings());
	}
}