<?php namespace Poppy\Extension\Fe\Http;

use Illuminate\Database\Eloquent\Collection;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\FileHelper;

class ApiDocController extends Controller
{

	protected $self_menu;

	public function __construct()
	{
		parent::__construct();
		$this->self_menu = [
			'接口文档'   => config('app.url') . '/docs/dailian/',
			'apiDoc' => 'http://apidocjs.com',
		];
		\View::share([
			'self_menu' => $this->self_menu,
		]);
	}

	/**
	 * 获取接口列表
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$apidocDir = base_path('public/docs/dailian/api_data.json');
		$api       = json_decode(file_get_contents($apidocDir));
		$content   = new Collection($api);
		$group     = $content->groupBy('groupTitle');
		return view('ext-fe::index', [
			'group' => $group,
		]);
	}


	/**
	 * 自动生成接口
	 * @param string $type
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 */
	public function auto($type = '')
	{
		$catalog = config('duoli-api_doc.catalog');
		if (!$catalog) {
			return Resp::web(Resp::ERROR, "尚未配置 apidoc 生成目录");
		}
		if (!$type) {
			$keys = array_keys($catalog);
			$type = $keys[0];
		}

		try {
			$index   = \Input::get('url');
			$version = \Input::get('version', '1.0.0');
			$method  = \Input::get('method', 'get');

			$data = $this->apiData($type, $index, $method, $version);
			if (isset($data['current_params'])) {
				foreach ($data['current_params'] as $current_param) {
					if (!isset($data['params'][$current_param->field]) && !$current_param->optional) {
						$data['params'][$current_param->field] = $this->getParamValue($current_param);
					}
				}
			}

			$data['version'] = 'v' . substr($version, 0, strpos($version, '.'));

			$key = 'Success 200';
			if (isset($data['current']->success->fields->$key)) {
				$success = $data['current']->success->fields->$key;
			}
			else {
				$success = [];
			}

			// user
			$user  = [];
			$front = [];
			if (!isset($data['current'])) {
				return Resp::web(Resp::ERROR, "没有找到对应 URL 地址");
			}
			return view('l5-api_doc::auto', [
				'data'    => $data,
				'success' => $success,
				'user'    => $user,
				'front'   => $front,
				'api_url' => (config('api.domain') ? 'http://' . config('api.domain') : config('app.url')) . '/' . config('api.prefix'),
			]);
		} catch (\Exception $e) {
			return Resp::web(Resp::ERROR, $e->getMessage());
		}
	}


	protected function apiData($type, $prefix = null, $method = 'get', $version = '1.0.0')
	{
		$catalog  = config('duoli-api_doc.catalog');
		$docs     = $catalog[$type];
		$jsonFile = base_path($docs['doc'] . '/api_data.json');
		$data     = [];
		if (file_exists($jsonFile)) {
			$data['file_exists'] = true;
			$data['url_base']    = config('app.url') . '/' . config('api.prefix');
			$data['content']     = json_decode(FileHelper::get($jsonFile));
			$content             = new Collection($data['content']);
			$group               = $content->groupBy('groupTitle');
			$data['group']       = $group;
			$data['versions']    = [];
			$url                 = $prefix;
			if (!$url) {
				$url = '/' . $type . '/' . trim($docs['default_url'], '/');
			}
			if ($url) {
				foreach ($content as $key => $val) {
					$valUrl = trim($val->url, '/');
					$url    = trim($url, '/');
					if ($val->type == $method && $valUrl == $url && $val->version == $version) {
						$data['index']   = $url;
						$data['current'] = $val;

						if (isset($data['current']->parameter)) {
							$data['current_params'] = $data['current']->parameter->fields->Parameter;
							// $data['params']         = $this->params($data['current']);
						}
					}
					if ($val->type == $method && $valUrl == $url) {
						$vk                          = substr($val->version, 0, strpos($val->version, '.'));
						$data['versions']['v' . $vk] = $val->version;
					}
				}
			}
		}
		else {
			$data['file_exists'] = false;
		}
		if (isset($data['versions'])) {
			ksort($data['versions']);
		}

		return $data;
	}

}

