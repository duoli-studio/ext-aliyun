<?php namespace User\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use User\Models\PamAccount;

class Pam
{
	use SystemTrait;

	private $pamTable;

	public function __construct()
	{
		$this->pamTable = (new PamAccount())->getTable();
	}

	/**
	 * 用户注册
	 * @param string $mobile
	 * @param string $password
	 * @return bool
	 */
	public function register($mobile, $password)
	{
		// 组织数据 -> 根据数据库字段来组织
		$initDb = [
			'mobile'   => strval($mobile),
			'password' => strval($password),
		];

		// 验证数据
		$validator = \Validator::make($initDb, [
			'mobile'   => [
				Rule::required(),
				Rule::mobile(),
				Rule::unique($this->pamTable),
			],
			'password' => [
				Rule::required(),
				Rule::password(),
				Rule::string(),
				Rule::between(6, 16),
			],
		], [], [
			'mobile'   => '手机号',  // todo 多语言
			'password' => '密码',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// 处理数据库
		// 使用 事务
		// todo 加密算法需要处理
		if (!PamAccount::create($initDb)) {
			// 注册用户

			// 给用户默认角色

			// 触发注册成功的事件

			return false;
		}
		return true;
	}
}