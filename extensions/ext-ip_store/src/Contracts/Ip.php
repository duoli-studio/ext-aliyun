<?php namespace Poppy\Extension\IpStore\Contracts;

interface Ip {

	/**
	 * 发送短信
	 * @param $ip
	 * @return string
	 */
	public function area($ip);

	
}
