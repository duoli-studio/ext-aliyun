<?php namespace System\Tests\Module;


use Poppy\Framework\Application\TestCase;

class SystemTest extends TestCase
{
	public function testRepository()
	{
		// dd(get_included_files());
		$config = app('module')->get('system')->toArray();
		echo json_encode($config);
	}

	public function testMenu()
	{
		// dd(get_included_files());
		dd(app('module')->menus());
	}

	public function testHas()
	{
		// dd(get_included_files());
		dd(app('module')->has('system'));
	}

	public function testPages()
	{
		// dd(get_included_files());
		dd(app('module')->pages());
	}
}