<?php namespace System\Request\Api;

use Poppy\Framework\Application\Controller;
use System\Classes\Traits\SystemTrait;

/**
 * Class MenuController.
 */
class MenusController extends Controller
{
	use SystemTrait;

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function list()
	{
		return $this->getResponse()->json([
			'data'      => $this->getModule()->menus()->structures()->toArray(),
			'message'   => '获取菜单数据成功！',
			'originals' => $this->getModule()->menus()->toArray(),
		]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update()
	{
		$data = $this->getRequest()->input('data');
		foreach ($data as $key => $value) {
			unset($data[$key]['icon']);
			unset($data[$key]['parent']);
			unset($data[$key]['expand']);
			unset($data[$key]['path']);
			unset($data[$key]['permission']);
		}
		$this->getSetting()->set('administration.menus', json_encode($data));
		$this->getCache('poppy')->flush();
		return $this->getResponse()->json([
			'message' => '批量更新数据成功！',
		]);
	}
}
