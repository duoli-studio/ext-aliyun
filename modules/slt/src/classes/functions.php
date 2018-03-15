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
		if (\Poppy\Framework\Helper\UtilHelper::isUrl($url) || !$url) {
			$url                 = $url ?: config('app.url') . '/modules/system/images/no_pic.jpg';
			$attributes['title'] = $attributes['title'] ?? '单击可打开图片, 按住 `ctrl` + `鼠标` 点击可以查看原图';
			$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' J_image_preview' : 'J_image_preview';
		}
		else {
			$url = '/modules/slt/images/' . $url;
		}
		return Html::image($url, $alt, $attributes);
	}
}


if (!function_exists('slt_upload')) {
	/**
	 * slt image
	 * @param        $name
	 * @param        $value
	 * @param string $ext
	 * @return \Illuminate\Support\HtmlString
	 */
	function slt_upload($name, $value, $ext = 'image')
	{
		$url = route('slt:util.image');
		$id  = \Poppy\Framework\Helper\HtmlHelper::nameToId($name);
		return app('poppy.form')->webuploader($name, $value, [
			'id'         => $id,
			'ext'        => $ext,
			'upload_url' => $url,
			'field'      => 'image_file',
		]);
	}
}