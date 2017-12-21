<?php namespace Slt\Classes;


use User\Models\PamAccount;
use Slt\Models\UserProfile;

/**
 * 前台用户
 */
class FeUser
{

	/**
	 * @var PamAccount
	 */
	protected $pam;


	protected static $instance;

	/**
	 * @var UserProfile
	 */
	protected $profile;


	/**
	 * @var string
	 */
	protected $platform = 'web';

	/**
	 * Create a new instance of this singleton.
	 */
	final public static function instance()
	{
		return isset(static::$instance)
			? static::$instance
			: static::$instance = new static;
	}

	/**
	 * @param PamAccount $pam
	 * @param string     $platform 平台
	 * @return FeUser|bool
	 */
	public function init(&$pam, $platform = 'web')
	{
		// 这里保证只能够初始化一次
		if ($this->pam) {
			return $this;
		}
		$this->pam      = $pam;
		$this->platform = $platform;

		$this->profile = UserProfile::firstOrCreate([
			'account_id' => $this->pam->id,
		], [
			'permission' => '',
		]);
		return $this;
	}


	/**
	 * @return PamAccount|null
	 */
	public function getPam(): PamAccount
	{
		return $this->pam;
	}

	/**
	 * @return UserProfile
	 */
	public function getProfile(): UserProfile
	{
		return $this->profile;
	}

	/**
	 * 是否启用
	 * @return bool
	 */
	public function pamIsEnable()
	{
		return ($this->pam->is_enable == BaseConfig::YES);
	}

	/**
	 * 获取当前操控的平台
	 * @return string
	 */
	public function getPlatform()
	{
		return $this->platform;
	}

}