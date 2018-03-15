<?php namespace System\Request\Develop;

use Curl\Curl;
use Illuminate\Database\Eloquent\Collection;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\FileHelper;
use Poppy\Framework\Helper\RawCookieHelper;
use Poppy\Framework\Helper\StrHelper;

class ApiDocController extends Controller
{
	protected $self_menu;

	public function __construct()
	{
		parent::__construct();
		$catlog = config('fe.apidoc');

		if (count($catlog)) {
			foreach ($catlog as $api_doc) {
				if (isset($api_doc['title']) && $api_doc['title']) {
					$this->self_menu[$api_doc['title']] = config('app.url') . ltrim($api_doc['doc'], 'public');
				}
			}
		}
		$this->self_menu['apiDoc'] = 'http://apidocjs.com';
		\View::share([
			'self_menu' => $this->self_menu,
		]);
	}

	/**
	 * 自动生成接口
	 * @param string $type
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 */
	public function auto($type = '')
	{
		$catalog = config('fe.apidoc');
		if (!$catalog) {
			return Resp::web(Resp::ERROR, '尚未配置 apidoc 生成目录');
		}
		if (!$type) {
			$keys = array_keys($catalog);
			$type = $keys[0];
		}

		$this->seo('Restful-' . $type, '优雅的在线接口调试方案');

		$tokenGet = function ($cookie_key) {
			if (RawCookieHelper::has($cookie_key)) {
				$token = RawCookieHelper::get($cookie_key);

				// check token is valid
				$curl   = new Curl();
				$access = route('system:pam.auth.access');
				$curl->setHeader('x-requested-with', 'XMLHttpRequest');
				$curl->setHeader('Authorization', 'Bearer ' . $token);
				$curl->post($access);
				if ($curl->httpStatusCode === 401) {
					RawCookieHelper::remove($cookie_key);
				}
			}

			return RawCookieHelper::get($cookie_key);
		};

		try {
			$index   = \Input::get('url');
			$version = \Input::get('version', '1.0.0');
			$method  = \Input::get('method', 'get');

			$data      = $this->apiData($type, $index, $method, $version);
			$variables = [];
			if (isset($data['current_params'])) {
				foreach ($data['current_params'] as $current_param) {
					if (!isset($data['params'][$current_param->field]) && !$current_param->optional) {
						if (starts_with($current_param->field, ':')) {
							$variableName             = trim($current_param->field, ':');
							$values                   = StrHelper::parseKey(strip_tags($current_param->description));
							$variables[$variableName] = $values;
						}
						else {
							$data['params'][$current_param->field] = $this->getParamValue($current_param);
						}
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

			$data['token'] = $tokenGet('dev_token#' . $type);

			// user
			$user  = [];
			$front = [];
			if (!isset($data['current'])) {
				return Resp::web(Resp::ERROR, '没有找到对应 URL 地址');
			}

			return view('system::develop.api_doc.auto', [
				'guard'     => $type,
				'data'      => $data,
				'variables' => $variables,
				'success'   => $success,
				'user'      => $user,
				'front'     => $front,
				'api_url'   => config('app.url'),
			]);
		} catch (\Exception $e) {
			return Resp::web(Resp::ERROR, $e->getMessage());
		}
	}

	protected function apiData($type, $prefix = null, $method = 'get', $version = '1.0.0')
	{
		$catalog  = config('fe.apidoc');
		$docs     = $catalog[$type];
		$jsonFile = base_path($docs['doc'] . '/api_data.json');
		$data     = [];
		if (file_exists($jsonFile)) {
			$data['file_exists'] = true;
			$data['url_base']    = config('app.url');
			$data['content']     = json_decode(FileHelper::get($jsonFile));
			$content             = new Collection($data['content']);
			$group               = $content->groupBy('groupTitle');
			$data['group']       = $group;
			$data['versions']    = [];
			$url                 = $prefix;
			if (!$url) {
				$url = '/' . trim($docs['default_url'] ?? '', '/');
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

	protected function getParamValue($param)
	{
		/*
		"group": "Parameter"
		"type": "<p>String</p> "
		"optional": false
		"field": "device_id"
		"size": "2..5"
		"description": "<p>设备ID, 设备唯一的序列号</p> "
		 */
		$type          = strtolower(strip_tags(trim($param->type)));
		$allowedValues = isset($param->allowedValues) ? $param->allowedValues : '';
		$size          = isset($param->size) ? $param->size : '';
		switch ($type) {
			case 'string':
				if (strpos($size, '..') !== false) {
					list($start, $end) = explode('..', $size);
					$start             = (int) $start;
					$end               = (int) $end;

					$length = rand($start, $end);

					return StrHelper::random($length);
				}
				if ($allowedValues) {
					shuffle($allowedValues);

					return $allowedValues[0];
				}

				return '';
				break;
			case 'boolean':
				return rand(0, 1);
				break;
			case 'number':
				if (strpos($size, '-') !== false) {
					list($start, $end) = explode('-', $size);
					$start             = (int) $start;
					$end               = (int) $end;

					return rand($start, $end);
				}
				if (strpos($size, '..') !== false) {
					list($start, $end) = explode('..', $size);
					$start             = (int) $start;
					$end               = (int) $end;

					$start = ((int) str_pad(1, $start, 0));
					$end   = ((int) str_pad(1, $end + 1, 0)) - 1;

					return rand($start, $end);
				}
				if ($allowedValues) {
					shuffle($allowedValues);

					return $allowedValues[0];
				}

				return rand(0, 99999999);
				break;
		}

		return '';
	}
}

