<?php namespace Poppy\Extension\IpStore\Repositories;

use Poppy\Extension\IpStore\Contracts\Ip as IpContract;

/**
 * Class Taobao
 * http://ip.taobao.com/
 * @package Poppy\Extension\IpStore\Repositories
 */
class Taobao extends Base implements IpContract {

	private $url = 'http://ip.taobao.com/service/getIpInfo.php?ip=__IP__';

	public function area($ip) {
		if ($this->isLocal($ip)) {
			return $this->localArea;
		}
		$ipInfo = file_get_contents(str_replace('__IP__', $ip, $this->url));
		$result = json_decode($ipInfo, true);
		if ($result['code'] == 0) {
			return $result['data']['country'];
		} else {
			return '';
		}
	}
}