<?php

namespace Poppy\Extension\Aliyun\Core;

use Poppy\Extension\Aliyun\Core\Regions\EndpointConfig;

//config http proxy
define('ENABLE_HTTP_PROXY', false);
define('HTTP_PROXY_IP', '127.0.0.1');
define('HTTP_PROXY_PORT', '8888');


class Config
{
	private static $loaded = false;

	public static function load()
	{
		if (self::$loaded) {
			return;
		}
		EndpointConfig::load();
		self::$loaded = true;
	}
}