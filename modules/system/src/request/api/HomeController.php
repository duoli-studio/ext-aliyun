<?php namespace System\Request\Api;

use Illuminate\Support\Collection;
use Poppy\Framework\Application\Controller;
use System\Classes\Traits\SystemTrait;

/**
 * Class ConfigurationsController.
 */
class HomeController extends Controller
{
	use SystemTrait;

	/**
	 * Get page defined
	 * @param $path
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function page($path)
	{
		$page = $this->getBackend()->pages()->filter(function ($definition) use ($path) {
			return $definition['initialization']['path'] == $path;
		})->first();

		if (isset($page['tabs']) && $page['tabs'] instanceof Collection && count($page['tabs']) > 0) {

			foreach ($page['tabs'] as $key => $tab){
				$tab['submit'] = url($tab['submit']);
				$page['tabs'][$key] = $tab;
			}
		}
		return $this->getResponse()->json([
			'data'    => $page,
			'message' => '获取数据成功！',
		]);
	}
}
