<?php namespace User\Action;

use User\models\PamAccount;
use Poppy\Framework\Classes\Traits\AppTrait;
use Carbon\Carbon;

class ActRegister
{
	/**
	 * 用户注册
	 * @param $username
	 * @param $password
	 * @param $re_password
	 * @param $mobile
	 * @return bool|string
	 */
	public function register($username, $password, $re_password, $mobile)
	{
		$validator = \Validator::make([
			'username'    => $username,
			'password'    => $password,
			're_password' => $re_password,
			'mobile'      => $mobile,
		], [
			'username'    => 'required',
			'password'    => 'required|min:6|max:16',
			're_password' => 'required|min:6|max:16',
			'mobile'      => 'required',
		], [
			'username'    => '必填',
			'password'    => '必填且最小长度为6',
			're_password' => '必填',
			'mobile'      => '必填',
		]);

		if ($validator->fails()) {
			return false;
		}

		if (!$this->isMobile($mobile)) {
			return '手机号格式不对,请重新输入';
		}

		$account_mobile = PamAccount::all('mobile')->toArray();
		foreach($account_mobile as $mobile_1)
		{
			if (in_array($mobile, $mobile_1)) {
				return AppTrait::setError('该手机号已经存在');
			}
		}

		if ($re_password !== $password) {
			return AppTrait::setError('两次输入的密码不一致');
		}
		$password = encrypt($password);

		$data   = [
			'username'   => $username,
			'password'   => $password,
			'mobile'     => $mobile,
			'logined_at' => Carbon::now(),
		];
		$result = PamAccount::create($data);
		if ($result) {
			return '注册成功';
		}
		else {
			return AppTrait::setError('注册失败');
		}
	}

	/**
	 * 验证手机号
	 * @param $mobile
	 * @return false|int
	 */
	private function isMobile($mobile)
	{
		$re = "/^1[3|4|5|8][0-9]\d{4,8}$/";
		return preg_match($re, $mobile);
	}
}