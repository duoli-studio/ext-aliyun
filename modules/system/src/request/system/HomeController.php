<?php namespace System\Request\System;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Poppy\Framework\Helper\RawCookieHelper;
use System\Classes\Traits\SystemTrait;

/**
 * 后端入口
 */
class HomeController extends Controller
{
	use SystemTrait, ViewTrait;


	/**
	 * @return \Response
	 */
	public function layout()
	{
		$this->share('translations', json_encode($this->getTranslator()->fetch('zh')));
		return view('system::layout');
	}
}
