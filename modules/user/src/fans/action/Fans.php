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
		if (!UserFans::where('account_id', $account_id)->where('fans_id', $this->pam->id)->exists()) {
			$this->fans = UserFans::create($initDb);
			return true;
		}
		else {
			return $this->setError('已关注');
		}
	}

	/**
	 * 取关
	 * @param int $account_id
	 * @return bool
	 * @throws \Exception
	 */
	public function cancel($account_id)
	{
		if (!$this->checkPermission()) {
			return false;
		}
		if (!empty($account_id)) {
			if (UserFans::where('account_id', $account_id)->where('fans_id', $this->pam->id)->exists()) {
				$result = UserFans::where('account_id', $account_id)->where('fans_id', $this->pam->id)->delete();
				if ($result && $result !== 0) {
					return true;
				}
				else {
					return $this->setError('已取关');
				}
			}
			else {
				return $this->setError('已取关');
			}
		}
		else {
			return $this->setError('取关人不能为空');
		}
	}
}