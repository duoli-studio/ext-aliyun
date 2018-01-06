<?php namespace User\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use User\Models\PamBind;
use User\Models\UserProfile;

class User
{
	use SystemTrait;

	/**
	 * @var UserProfile
	 */
	protected $profileTable;


	public function __construct()
	{
		$this->profileTable = (new UserProfile())->getTable();
	}

	/**
	 * 设置基本的信息
	 * @param string $nickname
	 * @param string $sex
	 * @param string $password
	 * @return bool
	 */
	public function register($nickname, $sex, $password)
	{
		if (!$this->checkPermission()) {
			return false;
		}

		$nickname = strval($nickname);

		$initDb = [
			'nickname'   => $nickname,
			'sex'        => $sex,
			'account_id' => $this->pam->id,
		];

		$rule = [
			'nickname' => [
				Rule::required(),
				Rule::unique($this->profileTable, 'nickname'),
				Rule::between(3, 30),
				Rule::string(),
			],
			'sex'      => [
				Rule::required(),
			],
		];

		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		try {
			// 处理数据库
			return \DB::transaction(function() use ($initDb, $password) {

				app('act.pam')->setPassword($this->pam, $password);
				UserProfile::create($initDb);

				return true;
			});
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		} catch (\Throwable $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 解绑第三方账号
	 * @param string $type 第三方账号的类型
	 * @return bool
	 */
	public function unbind($type)
	{
		if (!$this->checkPermission()) {
			return false;
		}

		$validator = \Validator::make([
			'type' => $type,
		], [
			'type' => 'required|in:qq,wx',
		], [], [
			'type' => '第三方绑定账号类型',
		]);

		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if ($type == 'qq') {
			PamBind::where('account_id', $this->pam->id)->update([
				'qq_key'      => '',
				'qq_union_id' => '',
			]);
			return true;
		}
		if ($type == 'wx') {
			PamBind::where('account_id', $this->pam->id)->update([
				'wx_key'      => '',
				'wx_union_id' => '',
			]);
			return true;
		}
		return $this->setError('第三方绑定账号类型错误');
	}

}