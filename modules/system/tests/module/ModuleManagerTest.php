<?php namespace System\Tests\Module;


use Poppy\Framework\Application\TestCase;

class ModuleManagerTest extends TestCase
{
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
		dd(app('module')->pages());
	}
}