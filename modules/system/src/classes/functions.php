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
	 * @throws \Illuminate\Container\EntryNotFoundException
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
