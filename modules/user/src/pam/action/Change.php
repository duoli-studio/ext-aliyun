<?php namespace User\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use User\Models\UserProfile;

class Change
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


	public function nicknameChange($nickname)
	{
		if (!$this->checkPermission()) {
			return false;
		}

		//获取原来的昵称 显示用
		// $nickname = UserProfile::where('id', $accountId)->get('nickname');

		//验证规则
		$newNick   = [
			'nickname' => strval($nickname),
		];
		$validator = \Validator::make($newNick, [
			'nickname' => [
				Rule::required(),
				Rule::string(),
				Rule::between(2, 16),
				Rule::unique($this->profileTable, 'nickname')->where(function ($query) {
					$query->where('id', '!=', $this->pam->id);
				}),
			],

		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		UserProfile::where('id', $this->pam->id)->update([
			'nickname' => $nickname,
		]);
		return true;
	}

	public function sexChange($accountId, $newSex)
	{

		//判断是否认证过
		if (UserProfile::where('id', $accountId)->where('is_chid_validated', 1)->count()) {
			return $this->setError('该账号已经实名认证过，不能修改性别');
		}
		else {
			//没有认证过的可以修改

			UserProfile::where('id', $accountId)->update(['sex' => $newSex]);
		}
		return true;

	}

	//编辑签名
	public function signChange($accountId, $signature)
	{

		//获取原来的签名 显示用
		//验证规则 验证长度
		$newSign   = [
			'signature' => strval($signature),
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
		UserProfile::where('id', $accountId)->update(['signature' => "$signature"]);
		return true;

	}


	//游戏名片

	public function gameChange($accountId, $data)
	{
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

		UserProfile::where('id', $accountId)->update($array);
		// $this->userTable->save();
		return true;
	}


	//修改头像
	public function headPicChange($accountId, $headPic)
	{
		//检查上传头像是否符合规则

		//上传到阿里云oss 调用方法

		//修改头像
		$this->profileTable->where('id', $accountId)->update(['head_pic' => "$headPic"]);
		return true;
	}


}