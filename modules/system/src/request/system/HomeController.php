<?php namespace System\Request\System;

use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use Poppy\Framework\Classes\Traits\ViewTrait;

/**
 * 后端入口
 */
class HomeController extends Controller
{
	use PoppyTrait, ViewTrait;

	/**
	 * @return \Response
	 */
	public function layout()
	{
		$this->share('translations', json_encode($this->getTranslator()->fetch('zh')));
		return view('system::layout');
	}

	public function graphi()
	{
		return view('system::graphql.graphiql', [
			'graphqlPath' => url('api/system/graphql'),
		]);
	}
}
