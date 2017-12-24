<?php

if (!function_exists('slt_image')) {
	/**
	 * slt image
	 * @param string      $url
	 * @param string|null $alt
	 * @param array       $attributes
	 * @return \Illuminate\Support\HtmlString
	 */
	function slt_image($url, $alt = null, $attributes = [])
	{
		return Html::image('modules/slt/images/' . $url, $alt, $attributes);
	}
}