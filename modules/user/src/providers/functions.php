<?php


if (!function_exists('pam_acl')) {
	/**
	 * 获取 acl
	 * @param $id
	 * @param $key
	 * @return mixed
	 */
	function pam_acl($id, $key)
	{
		$acl  = Poppy\Base\Models\BaseConfig::getCache('acl');
		$auth = array_get($acl, 'account_id_' . $id);
		if ($auth) {
			$auth = json_decode($auth, true);
		}
		else {
			$auth = [];
		}
		return array_get($auth, $key);
	}
}
