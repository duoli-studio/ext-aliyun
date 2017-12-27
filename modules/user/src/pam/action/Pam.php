<?php namespace User\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamRole;
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
	 * @throws \Throwable
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
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// 服务器处理
		// role and account type
		$role = PamRole::where('name', PamRole::FE_USER)->first();
		if (!$role) {
			return $this->setError('给定的用户角色不存在');
		}


		try {
			// 处理数据库
			return \DB::transaction(function () use ($initDb, $role) {
				/** @var PamAccount $pam pam account */
				$pam = PamAccount::create($initDb);

				// 给用户默认角色
				$pam->roles()->attach($role->id);

				// 触发注册成功的事件
			});
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}
}