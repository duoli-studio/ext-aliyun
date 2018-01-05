<?php namespace User\Fans\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use User\Models\UserFans;

class Fans
{
	use SystemTrait;

	/**
	 * @var UserFans
	 */
	protected $fans;

	/**
	 * 关注
	 * @param int $account_id
	 * @return bool
	 */
	public function concern($account_id)
	{
		if (!$this->checkPermission()) {
			return false;
		}
		$initDb    = [
			'account_id' => $account_id,
			'fans_id'    => $this->pam->id,
		];
		$validator = \Validator::make($initDb, [
			'account_id' => [
				Rule::required(),
				Rule::integer(),
			],
		], [], [
			'account_id' => trans('user::fans.db.account_id'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		$result = UserFans::where('account_id', $account_id)->where('fans_id', $this->pam->id)->get();
		if (!$result){
			$this->fans = UserFans::create($initDb);
		}
		return true;
	}

	/**
	 * 取关
	 * @param int $account_id
	 * @param     $pam
	 * @return bool
	 * @throws \Exception
	 */
	public function canceled($account_id, $pam)
	{
		$this->pam = $pam;
		if (!empty($account_id)) {
			$result = UserFans::where('account_id', $account_id)->where('fans_id', $this->pam->id)->delete();
			return $result && $result !== 0 ? true : false;
		}
		else {
			return false;
		}
	}
}