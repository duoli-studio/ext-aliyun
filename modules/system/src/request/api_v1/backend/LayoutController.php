<?php namespace System\Request\ApiV1\Backend;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Module\Module;
use System\Setting\Events\SettingUpdated;

class LayoutController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                 {get} api_v1/backend/system/layout/menus [O]Layout-Menus
	 * @apiVersion          1.0.0
	 * @apiName             LayoutMenus
	 * @apiGroup            System
	 */
	public function menus()
	{
		if (is_post()) {
			$data = $this->getRequest()->input('data');
			foreach ($data as $key => $value) {
				unset($data[$key]['icon']);
				unset($data[$key]['parent']);
				unset($data[$key]['expand']);
				unset($data[$key]['path']);
				unset($data[$key]['permission']);
			}
			$this->getSetting()->set('system::system.menus', json_encode($data));
			$this->getCache('poppy')->flush();

			return $this->getResponse()->json([
				'message' => '批量更新数据成功！',
			]);
		}

		return $this->getResponse()->json([
			'data'      => $this->getModule()->menus()->structures()->toArray(),
			'message'   => '获取菜单数据成功！',
			'originals' => $this->getModule()->menus()->toArray(),
		]);
	}

	/**
	 * @api                 {get} api_v1/backend/system/layout/dashboards [O]Layout-Dashboards
	 * @apiVersion          1.0.0
	 * @apiName             LayoutDashboards
	 * @apiGroup            System
	 * @apiPermission       backend
	 * @apiSuccess {String}  title                           描述
	 * @apiSuccess {String}  root                            根
	 * @apiSuccess {String}  groups                          所有分组
	 * @apiSuccess {String}  groups.group                    分组
	 * @apiSuccess {String}  groups.title                    标题
	 * @apiSuccess {String}  groups.permissions              所有权限
	 * @apiSuccess {String}  groups.permissions.description  描述
	 * @apiSuccess {String}  groups.permissions.id           权限ID
	 * @apiSuccess {String}  groups.permissions.value        选中值
	 * @apiSuccessExample   data:
	 * [
	 *     {
	 *         "title": "全局",
	 *         "root": "global",
	 *         "groups": [
	 *             {
	 *                 "group": "addon",
	 *                 "title": "插件权限",
	 *                 "permissions": [
	 *                     {
	 *                         "description": "全局插件管理权限",
	 *                         "id": 1,
	 *                         "value": 0
	 *                     }
	 *             },
	 *         ]
	 *     }
	 * ]
	 */
	public function dashboards()
	{
		if (is_post()) {
			$this->validate($this->getRequest(), [
				'hidden' => [
					Rule::array(),
				],
				'left'   => [
					Rule::array(),
				],
				'right'  => [
					Rule::array(),
				],
			], [
				'hidden.array' => '隐藏数据必须为数组',
				'left.array'   => '左侧数据必须为数组',
				'right.array'  => '右侧数据必须为数组',
			]);
			$data = collect();
			$data->put('hidden', collect($this->getRequest()->input('hidden', []))->transform(function (array $data) {
				return $data['identification'];
			}));
			$data->put('left', collect($this->getRequest()->input('left', []))->transform(function (array $data) {
				return $data['identification'];
			}));
			$data->put('right', collect($this->getRequest()->input('right', []))->transform(function (array $data) {
				return $data['identification'];
			}));
			$this->getSetting()->set('administration.dashboards', json_encode($data->toArray()));

			return $this->response->json([
				'message' => '保存仪表盘页面布局成功！',
			]);
		}
		$dashboards = collect();
		$hidden     = collect();
		$left       = collect();
		$right      = collect();
		$this->getModule()->enabled()->each(function (Module $module) use ($dashboards) {
			$module->offsetExists('dashboards') && collect($module->get('dashboards'))->each(function (
				$definition,
				$identification
			) use ($dashboards) {
				if (is_string($definition['template'])) {
					list($class, $method) = explode('@', $definition['template']);
					if (class_exists($class)) {
						$instance               = $this->getContainer()->make($class);
						$definition['template'] = $this->getContainer()->call([
							$instance,
							$method,
						]);
					}
				}
				$dashboards->put($identification, $definition);
			});
		});
		$dashboards = $dashboards->keyBy('identification');
		$saved      = collect(json_decode($this->getSetting()->get('administration.dashboards', '')));
		$saved->has('hidden') && collect($saved->get('hidden', []))->each(function ($identification) use (
			$dashboards,
			$hidden
		) {
			if ($dashboards->has($identification)) {
				$hidden->push($dashboards->get($identification));
				$dashboards->offsetUnset($identification);
			}
		});
		$saved->has('left') && collect($saved->get('left', []))->each(function ($identification) use (
			$dashboards,
			$left
		) {
			if ($dashboards->has($identification)) {
				$left->push($dashboards->get($identification));
				$dashboards->offsetUnset($identification);
			}
		});
		$saved->has('right') && collect($saved->get('right', []))->each(function ($identification) use (
			$dashboards,
			$right
		) {
			if ($dashboards->has($identification)) {
				$right->push($dashboards->get($identification));
				$dashboards->offsetUnset($identification);
			}
		});
		if ($dashboards->isNotEmpty()) {
			$dashboards->each(function ($definition) use ($left) {
				$left->push($definition);
			});
		}

		return $this->getResponse()->json([
			'data'    => [
				'hidden' => $hidden->toArray(),
				'left'   => $left->toArray(),
				'right'  => $right->toArray(),
			],
			'message' => '获取面板数据成功！',
		]);
	}

	/**
	 * @api                 {get} api_v1/backend/system/layout/page [O]Layout-Page
	 * @apiVersion          1.0.0
	 * @apiName             LayoutPage
	 * @apiGroup            System
	 */
	public function page($path)
	{
		if (is_post()) {
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

	/**
	 * @api                 {get} api_v1/backend/system/layout/information [O]Layout-Information
	 * @apiVersion          1.0.0
	 * @apiName             LayoutInformation
	 * @apiGroup            System
	 */
	public function information()
	{
		$data = $this->getResponse()->json([
			'data'    => [
				'navigation'  => $this->getBackend()->navigations()->toArray(),
				'pages'       => $this->getBackend()->pages()->toArray(),
				'scripts'     => $this->getBackend()->scripts()->toArray(),
				'stylesheets' => $this->getBackend()->stylesheets()->toArray(),
			],
			'message' => '获取模块和插件信息成功！',
		]);

		return $data;
	}
}
