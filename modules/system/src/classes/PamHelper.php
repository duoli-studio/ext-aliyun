<?php namespace System\Classes;


class PamHelper
{

	/**
	 * 生成密码串
	 * @param string $pwd  原始密码
	 * @param string $salt 随机盐值
	 * @return string
	 */
	public static function genPwd($pwd, $salt)
	{
		return md5(md5(sha1($pwd) . $salt) . $salt);
	}

}