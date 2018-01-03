<?php namespace System\Request\Api;

use Illuminate\Support\Collection;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Setting\Events\SettingUpdated;

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
		if (is_post()) {
			$this->validate($this->getRequest(), [
				'input.value' => Rule::required(),
			], [
				'input.value.required' => '单行输入框必须填写',
			]);
			$inputs = json_decode(\Input::getContent(), true);
			foreach ($inputs as $input) {
				$this->getSetting()->set($input['key'], $input['value'] ?? $input['default']);
			}

			$this->getEvent()->dispatch(new SettingUpdated());

			return Resp::web(Resp::SUCCESS, '更新成功');
		}
		$page = $this->getBackend()->pages()->filter(function ($definition) use ($path) {
			return $definition['initialization']['path'] == $path;
		})->first();

		if (isset($page['tabs']) && $page['tabs'] instanceof Collection && count($page['tabs']) > 0) {
			foreach ($page['tabs'] as $key => $tab) {
				$tab['submit']      = url($tab['submit']);
				$page['tabs'][$key] = $tab;
			}
		}
		return $this->getResponse()->json([
			'data'    => $page,
			'message' => '获取数据成功！',
		]);
	}
}
