<?php namespace System\Tests\Extension;

use Poppy\Framework\Application\TestCase;

class ExtensionTest extends TestCase
{
	public function testExtension()
	{
		// \Artisan::call('cache:clear');

		dd(app('extension')->has('poppy/ext-fe'));

		$extensions = app('extension')->repository();

		dd($extensions);
	}
}