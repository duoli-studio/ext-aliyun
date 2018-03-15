<?php namespace System\Classes;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Traits\KeyParserTrait;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;

class SettingUI
{
	use SystemTrait, KeyParserTrait;

	/**
	 * Init
	 * @var array
	 */
	private $initialization = [];

	/**
	 * Tabs
	 * @var array
	 */
	private $tabs = [];

	/**
	 * 页面
	 * @var array
	 */
	private $pages = [];

	/**
	 * Flat Tab
	 * @var array
	 */
	private $flatTab = [];

	/**
	 * Path
	 * @var string
	 */
	private $path = '';

	/**
	 * 请求地址
	 * @var
	 */
	private $url;

	/**
	 * SettingManager constructor.
	 * @param $key
	 * @throws \Exception
	 */
	public function __construct($key)
	{
		try {
			$this->pages = $this->getBackend()->pages();
			$poppyPages  = config('poppy.backend_pages');
			if ($poppyPages) {
				$this->pages = $this->pages->filter(function ($page, $key) use ($poppyPages) {
					return in_array($key, $poppyPages);
				});
			}
			$definition           = $this->getBackend()->pages()->offsetGet($key);
			$this->initialization = $definition['initialization'];
			$this->tabs           = $definition['tabs'];
		} catch (\Exception $e) {
			throw new \Exception('配置文件不正确');
		}

		foreach ($this->tabs as $key => $tab) {
			foreach ($tab['fields'] as $f_key => $field) {
				if ($field['type'] == 'input') {
					$form = [
						'class'       => 'form-control',
						'placeholder' => $item['placeholder'] ?? '',
					];
				}
				elseif ($field['type'] == 'textarea') {
					$form = [
						'class'       => 'form-control',
						'rows'        => '3',
						'placeholder' => $item['placeholder'] ?? '',
					];
				}
				else {
					$form = [];
				}
				$form += $this->getValidates($field, 'js-validation');
				$field['options'] = $form;

				list($ns, $group, $item) = $this->parseKey($field['key']);
				unset($tab['fields'][$f_key]);
				if (!$ns || !$group || !$item) {
					continue;
				}
				$field['name']         = "{$ns}::{$group}::{$item}";
				$tab['fields'][$f_key] = $field;

				$this->flatTab[$field['name']] = $field;
			}
			$this->tabs[$key] = $tab;
		}

		$this->url = $this->url ?: \Input::url();
	}

	public function render()
	{
		return view('system::backend.tpl.setting', [
			'title'       => $this->initialization['name'],
			'description' => $this->initialization['description'] ?? $this->initialization['name'],
			'tabs'        => $this->tabs,
			'pages'       => $this->pages,
			'url'         => $this->url,
			'path'        => $this->path,
		]);
	}

	/**
	 * 更新配置
	 * @param Request $request
	 * @return bool
	 */
	public function save(Request $request)
	{
		$inputKeys = collect();
		$keys      = collect($request->keys());
		$configs   = [];
		$keys->each(function ($item) use ($inputKeys, $request, &$configs) {
			if (strpos($item, '::') !== false) {
				$configs[$item] = $request->get($item);
				$inputKeys->push($item);
			}
		});

		$rule  = [];
		$title = [];
		foreach ($this->flatTab as $key => $field) {
			if (!in_array($key, $inputKeys->toArray())) {
				continue;
			}
			$rule[$key]  = $this->getValidates($field, 'laravel');
			$title[$key] = array_get($field, 'label');
		}
		if ($rule) {
			$validator = \Validator::make($configs, $rule, [], $title);
			if ($validator->fails()) {
				return $this->setError($validator->messages());
			}
		}

		foreach ($configs as $key => $value) {
			$key = str_replace_last('::', '.', $key);
			$this->getSetting()->set($key, $value);
		}

		\Cache::forget('modules.page');

		return true;
	}

	private function getValidates($field, $return_type)
	{
		$validates = $field['validates'] ?? [];
		switch ($return_type) {
			case 'js-validation': // js 使用
			default:
				if (!count($validates)) {
					return [];
				}
				$rule = [];
				foreach ($validates as $validate) {
					$required = $validate['required'] ?? false;
					if ($required) {
						$rule += [
							'data-rule-required' => 'true',
							'data-msg-required'  => $validate['message'] ?? '此项必填',
						];
					}
				}

				return $rule;
				break;
			case 'laravel': // framework 使用
				$rule = [];
				if (isset($field['required']) && $field['required']) {
					$rule[] = Rule::required();
				}

				if (!count($validates)) {
					return [];
				}

				foreach ($validates as $validate) {
					$required = $validate['required'] ?? false;
					if ($required) {
						$rule[] = Rule::required();
					}
				}

				return array_unique($rule);
				break;
		}
	}
}