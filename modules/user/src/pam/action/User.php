<?php namespace User\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Models\PamBind;
use User\Models\UserProfile;

class User
{
	use SystemTrait;

	/**
	 * @var UserProfile
	 */
	protected $userProfile;

	/**
	 * @var string Pam table
	 */
	private $pamTable;

	public function __construct()
	{
		$this->pamTable    = (new PamAccount())->getTable();
		$this->userProfile = (new UserProfile())->getTable();
	}

	/**
	 * 解绑第三方账号
	 * @param string $type 第三方账号的类型, 暂时只支持 qq
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
			'type' => 'required|in:qq,alipay,weixin',
		], [], [
			'type' => '第三方绑定账号类型',
		]);

		if ($type == 'qq') {
			PamBind::where('account_id', $this->pam->id)->update([
				'qq_key'      => '',
				'qq_union_id' => '',
			]);
			return true;
		}
		if ($type == 'weixin') {
			PamBind::where('account_id', $this->pam->id)->update([
				'weixin_key'      => '',
				'weixin_union_id' => '',
			]);
			return true;
		}
		return $this->setError('第三方绑定账号类型错误');
	}

	/**
	 * 设置基本的信息
	 * @param $nickname
	 * @param $sex
	 * @param $id
	 * @return bool
	 */
	public function register($nickname, $sex, $id)
	{
		$nickname = strval($nickname);

		$initDb = [
			'nickname' => $nickname,
			'sex'      => $sex,
			'id'       => $id,
		];

		$rule = [
			'nickname' => [
				Rule::required(),
				Rule::unique($this->userProfile, 'nickname'),
				Rule::between(2, 12),
				Rule::string(),
			],
			'sex'      => [
				Rule::required(),
			],
			'id'       => [
				Rule::required(),
				Rule::unique($this->userProfile, 'id'),
			],
		];

		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		try {
			UserProfile::create($initDb);
			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

}