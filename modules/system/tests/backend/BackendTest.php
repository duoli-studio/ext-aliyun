<?php namespace System\Tests\Module;


use Poppy\Framework\Application\TestCase;
use System\Backend\_repositories\NavigationRepository;

class BackendTest extends TestCase
{


	public function testMenu()
	{
		// dd(get_included_files());
		$menus = app('module')->menus();
		(new NavigationRepository())->initialize(app('module')->menus()->structures());
	}

}