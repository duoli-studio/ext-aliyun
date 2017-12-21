<?php namespace System\Request\Api;

use Illuminate\Support\Str;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Traits\PoppyTrait;
use System\Classes\Traits\SystemTrait;

/**
 * Class ConfigurationsController.
 */
class ConfigurationController extends Controller
{
	use SystemTrait;

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function definition($path)
	{
		$path = Str::replaceFirst('-', '/', $path);
		$page = $this->getBackend()->pages()->filter(function ($definition) use ($path) {
			return $definition['initialization']['path'] == $path;
		})->first();

		return $this->getResponse()->json([
			'data'    => $page,
			'message' => '获取数据成功！',
		]);
	}
}
