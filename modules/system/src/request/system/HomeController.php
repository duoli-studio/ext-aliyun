<?php namespace System\Request\System;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;

/**
 * 后端入口
 */
class HomeController extends Controller
{
	use SystemTrait, ViewTrait;


	public function test()
	{
		$this->getSetting()->getNsGroup('system', 'site');
	}
}
