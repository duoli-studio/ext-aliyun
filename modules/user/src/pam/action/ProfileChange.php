<?php namespace User\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use User\Models\UserProfile;

class ProfileChange
{
	use SystemTrait;


	/**
	 * @var string Pam table
	 */
	private $profileTable;

	public function __construct()
	{
		$this->profileTable = (new UserProfile())->getTable();
	}


	public function ProfileChange($type, $data)
	{
		if (!$this->checkPermission()) {
			return false;
		}

		switch ($type) {
			case UserProfile::TYPE_NICKNAME :
				//验证规则
				$newNick   = [
					'nickname' => strval($data),
				];
				$validator = \Validator::make($newNick, [
					'nickname' => [
						Rule::required(),
						Rule::string(),
						Rule::between(2, 16),
						Rule::unique($this->profileTable, 'nickname')->where(function($query) {
							$query->where('account_id', '!=', $this->pam->id);
						}),
					],

				]);
				if ($validator->fails()) {
					return $this->setError($validator->messages());
				}
				UserProfile::where('account_id', $this->pam->id)->update([
					'nickname' => $data,
				]);
				return true;
				break;
			case UserProfile::TYPE_SEX :
				//判断是否认证过
				if (UserProfile::where('account_id', $this->pam->id)->where('is_chid_validated', 1)->count()) {
					return $this->setError('该账号已经实名认证，不能修改性别');
				} else {
					//没有认证过的可以修改
					UserProfile::where('account_id', $this->pam->id)->update([
						'sex' => $data,
					]);
				}
				return true;
				break;
			case UserProfile::TYPE_SIGNATURE :
				//验证规则 验证长度
				$newSign   = [
					'signature' => strval($data),
				];
				$validator = \Validator::make($newSign, [
					'signature' => [
						Rule::required(),
						Rule::string(),
						Rule::between(0, 255),
					],
				]);
				if ($validator->fails()) {
					return $this->setError($validator->messages());
				}

				//修改
				UserProfile::where('account_id', $this->pam->id)->update([
					'signature' => $data,
				]);
				return true;
				break;
			case UserProfile::TYPE_GAME :
				//获取修改的数据
				$array = [
					'wz_game_nickname'     => strval(array_get($data, 'wz_game_nickname')),
					'wz_game_system'       => strval(array_get($data, 'wz_game_system')),
					'wz_game_login_way'    => strval(array_get($data, 'wz_game_login_way')),
					'wz_good_at_position'  => strval(array_get($data, 'wz_good_at_position')),
					'wz_game_hero'         => strval(array_get($data, 'wz_game_hero')),
					'lol_game_nickname'    => strval(array_get($data, 'lol_game_nickname')),
					'lol_game_service'     => strval(array_get($data, 'lol_game_service')),
					'lol_good_at_position' => strval(array_get($data, 'lol_good_at_position')),
					'lol_game_hero'        => strval(array_get($data, 'lol_game_hero')),
					'pubg_game_nickname'   => strval(array_get($data, 'pubg_game_nickname')),
					'pubg_game_steamid'    => strval(array_get($data, 'pubg_game_steamid')),
					'pubg_game_rank'       => strval(array_get($data, 'pubg_game_rank')),
				];

				UserProfile::where('account_id', $this->pam->id)->update($array);
				return true;
				break;
			case UserProfile::TYPE_HEAD_PIC :
				//检查上传头像是否符合规则

				//上传到阿里云oss 调用方法

				//修改头像
				UserProfile::where('account_id', $this->pam->id)->update([
					'head_pic' => $data,
				]);
				return true;
				break;
			case UserProfile::TYPE_AREA :
				//修改地区

		}

	}

}