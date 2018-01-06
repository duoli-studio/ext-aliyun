<?php
/**
 * Copyright (C) Update For IDE
 */

if (!function_exists('sys_setting')) {
	/**
	 * Get System Setting
	 * @param string $key
	 * @param null   $default
	 * @return mixed
	 */
	function sys_setting($key, $default = null)
	{
		return app('setting')->get($key, $default);
	}
}