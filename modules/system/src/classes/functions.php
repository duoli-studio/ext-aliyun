<?php
/**
 * Copyright (C) Update For IDE
 */

use Illuminate\Support\Str;

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

if (!function_exists('sys_gen_order')) {
	/**
	 * 生成订单号
	 * @param string $prefix
	 * @return string
	 */
	function sys_gen_order($prefix = '')
	{
		$sequence = rand(1000, 9999);
		$current  = \Carbon\Carbon::now()->format('YmdHis');
		return sprintf("%s%s%s%s", strtoupper($prefix), $current, \Poppy\Framework\Helper\TimeHelper::micro(), sprintf("%'.04d", $sequence));
	}
}

if (!function_exists('sys_trans')) {
	/**
	 * translate line
	 * @param       $line
	 * @param array $replace
	 * @return string
	 */
	function sys_trans($line, $replace = [])
	{
		foreach ($replace as $key => $value) {
			$line = str_replace(
				[':' . $key, ':' . Str::upper($key), ':' . Str::ucfirst($key)],
				[$value, Str::upper($value), Str::ucfirst($value)],
				$line
			);
		}
		return $line;
	}
}


