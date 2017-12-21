<?php namespace System\Request\Api;

use Poppy\Framework\Application\Controller;
use System\Classes\Traits\SystemTrait;


/**
 * Class InformationsController.
 */
class InformationController extends Controller
{
	use SystemTrait;

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function list()
	{
		return $this->getResponse()->json([
			'data'    => [
				'navigation'  => $this->getBackend()->navigations()->toArray(),
				'pages'       => $this->getBackend()->pages()->toArray(),
				'scripts'     => $this->getBackend()->scripts()->toArray(),
				'stylesheets' => $this->getBackend()->stylesheets()->toArray(),
			],
			'message' => '获取模块和插件信息成功！',
		]);
	}
}
