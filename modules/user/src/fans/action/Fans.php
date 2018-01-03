<?php namespace User\Fans\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Models\UserFans;

class Fans
{
	use SystemTrait;

	/**
	 * @var UserFans
	 */
	protected $fans;

	/**
	 * @var PamAccount
	 */
	protected $pam;

	public function __construct()
	{
	}

	/**
	 * 关注
	 * @param int $account_id
	 * @param $pam
	 * @return bool
	 */
	public function concern($account_id ,$pam)
	{
		$this->pam = $pam;
		$initDb    = [
			'account_id' => $account_id,
			'fans_id'    => $this->pam->id,
		];
		$validator = \Validator::make($initDb, [
			'account_id' => [
				Rule::required(),
				Rule::integer(),
			],
			'fans_id'    => [
				Rule::required(),
				Rule::integer(),
			],

		], [], [
			'account_id' => trans('user::fans.db.account_id'),
			'fans_id'    => trans('user::fans.db.fans_id'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$this->fans = UserFans::create($initDb);

		return true;
	}

	/**
	 * 取关
	 * @param int $account_id
	 * @param $pam
	 * @return bool
	 * @throws \Exception
	 */
	public function canceled($account_id ,$pam)
	{
		$this->pam = $pam;
		if (!empty($account_id)){
			$result = UserFans::where('account_id',$account_id)->where('fans_id',$this->pam->id)->delete();
			return $result && $result !== 0 ? true : false;
		}else{
			return false;
		}
	}
}