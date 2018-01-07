<?php namespace Poppy\Framework\Classes\FormElement;


use Poppy\Framework\Contracts\FormElement as FormElementContract;

class Area extends FormTypeBase implements FormElementContract
{

	public function __construct($name, $setting, $value = null)
	{
		parent::__construct($name, $setting, $value);
		$this->defaultValue                = isset($setting['area_default_value']) ? $setting['area_default_value'] : '';
		$this->placeholder                 = isset($setting['area_placeholder']) ? $setting['area_placeholder'] : '';
		$this->form_options['placeholder'] = $this->placeholder;
	}

	/**
	 * 渲染HTML
	 * @return mixed
	 */
	public function render()
	{
		return \Form::areaLinkage($this->name, $this->value, $this->form_options);
	}
}