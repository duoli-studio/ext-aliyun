<?php namespace System\Models;

/**
 * App\Models\PamRoleAccount
 *
 * @property int $account_id 账户id
 * @property int $role_id    角色id
 */
class PamRoleAccount extends \Eloquent
{
	protected $table = 'pam_role_account';

	protected $primaryKey = 'account_id';

	public $timestamps = false;

	protected $fillable = [
		'account_id',
		'role_id',
	];

	/**
	 * 通过账户id 获取角色id, 加入角色ID 缓存
	 * @param $account_id
	 * @return mixed
	 */
	public static function getRoleIdByAccountId($account_id)
	{
		static $rel = [];
		if (!isset($rel[$account_id])) {
			$rel[$account_id] = self::where('account_id', $account_id)->value('role_id');
		}

		return $rel[$account_id];
	}
}