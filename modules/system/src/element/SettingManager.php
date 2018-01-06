<?php namespace System\Element;

use Poppy\Framework\Classes\Traits\KeyParserTrait;
use System\Classes\Traits\SystemTrait;


/**
 * 用户UI界面
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2017 Sour Lemon Team
 */
class SettingManager
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
	 * Path
	 * @var string
	 */
	private $path = '';

	/**
	 * 表单配置
	 * @var array|mixed|string
	 */
	private $formSetting = [];

	/**
	 * 当前的类型
	 * @var
	 */
	private $type;

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
			$definition           = $this->getBackend()->pages()->offsetGet($key);
			$this->initialization = $definition['initialization'];
			$this->tabs           = $definition['tabs'];
		} catch (\Exception $e) {
			throw new \Exception('配置文件不正确');
		}
		$this->url = $this->url ?: \Input::url();
	}


	/**
	 * 获取设置信息
	 * @param        $key
	 * @param string $default
	 * @return string
	 */
	public function getDefaultValue($key, $default = '')
	{
		return $this->getSetting()->get($key, $default);
	}

	public function render()
	{
		foreach ($this->tabs as $key => $tab) {
			foreach ($tab['fields'] as $f_key => $field) {
				if ($field['type'] == 'input') {
					$form = [
						'class'       => 'form-control',
						'placeholder' => $item['placeholder'] ?? '',
					];
				}
				else {
					$form = [];
				}
				$form                  += $this->getValidates($field, 'js-validation');
				$field['options']      = $form;
				$tab['fields'][$f_key] = $field;
			}
		}
		return view('system::backend.tpl.setting', [
			'title'       => $this->initialization['name'],
			'description' => $this->initialization['description'] ?? $this->initialization['name'],
			'tabs'        => $this->tabs,
			'url'         => $this->url,
			'path'        => $this->path,
		]);
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
							'data-rule-required' => "true",
							'data-msg-required'  => $validate['message'] ?? '此项必填',
						];
					}
				}
				return $rule;
				break;
		}

	}

	/**
	 * 更新配置
	 * @param array  $configs
	 * @param string $group
	 * @return bool
	 */
	public function save($configs, $group = '')
	{
		$definition = $this->typeDefinition;
		$batch      = [];
		$keys       = [];

		$rule  = [];
		$title = [];
		foreach ($definition as $key => $value) {
			if (!$group) {
				if (array_get($value, 'validator')) {
					$rule[$key] = $value['validator'];
				}
			}
			if ($group == '_other') {
				if (array_get($value, 'validator') && !array_get($value, 'group')) {
					$rule[$key] = $value['validator'];
				}
			}
			elseif (array_get($value, 'validator') && array_get($value, 'group') == $group) {
				$rule[$key] = $value['validator'];
			}
			$title[$key] = array_get($value, 'title');
		}

		if ($rule) {
			$validator = \Validator::make($configs, $rule, [], $title);
			if ($validator->fails()) {
				return $this->setError($validator->messages());
			}
		}


		foreach ($configs as $key => $value) {
			if (isset($definition[$key])) {
				$keys[]  = $key;
				$batch[] = [
					'namespace'   => $this->namespace,
					'group'       => $this->type,
					'item'        => $key,
					'value'       => serialize($value),
					'description' => $definition[$key]['title'],
				];
			}
		}
		if ($keys) {
			BaseConfig::whereIn('item', array_keys($configs))
				->where('group', $this->type)
				->where('namespace', $this->namespace)
				->delete();
			BaseConfig::insert($batch);
		}
		\Cache::flush();
		return true;
	}
}