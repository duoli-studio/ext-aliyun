<?php namespace System\Request\System;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\PoppyTrait;
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

	public function graphi($schema = 'default')
	{
		if ($schema == 'default') {
			$schema = '';
		}
		$token = RawCookieHelper::get('dev_dianjing#token');
		return view('system::graphql.graphiql', [
			'graphqlPath' => url('api/system/graphql/' . $schema),
			'token'       => $token,
		]);
	}

	public function test()
	{
		$this->getSetting()->getNsGroup('system', 'site');
	}
}
