<?php namespace Poppy\Tests\Poppy;

use Poppy\Framework\Application\TestCase;
use Poppy\Framework\Helper\ArrHelper;

class PoppyTest extends TestCase
{
	public function testAll()
	{
		dd(app('poppy')->all());
	}

	public function testGenKey()
	{
		$arr    = [
			'location' => 'http://www.baidu.com',
			'status'   => 'error',
		];
		$genKey = ArrHelper::genKey($arr);

		// 组合数组
		$this->assertEquals('location|http://www.baidu.com;status|error', $genKey);

		// 组合空
		$this->assertEquals('', ArrHelper::genKey([]));
	}

	public function testModulePath()
	{
		echo app('path.module');
	}

	public function testModule()
	{
		$module = app('module')->repository()->get('system');
		dd($module);
	}
}