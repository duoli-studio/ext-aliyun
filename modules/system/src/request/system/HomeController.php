<?php namespace System\Request\System;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
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

	/**
	 * 返回示例json 数据
	 */
	public function json($url)
	{
		$url    = trim($url, '/');
		$urls   = explode('/', $url);
		$module = array_shift($urls);
		$file   = poppy_path($module, 'resources/json/' . implode('/', $urls) . '.json');
		if (file_exists($file)) {
			return \Response::make($this->getFile()->get($file));
		}
		 
			return Resp::web(Resp::ERROR, 'no file', 'json|1');
	}
}
