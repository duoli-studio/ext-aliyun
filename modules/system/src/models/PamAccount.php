<?php namespace System\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable as TraitAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use System\Rbac\Traits\RbacUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubjectAuthenticatable;


/**
 * @property int                       $id
 * @property string                    $mobile             手机号
 * @property string                    $username           用户名称
 * @property string                    $password           用户密码
 * @property Carbon                    $logined_at         注册时间
 * @property string                    $account_name       账号名称， 支持中文
 * @property string                    $account_pwd        账号密码
 * @property string                    $account_key        账号注册时候随机生成的6位key
 * @property string                    $account_type       账户类型
 * @property integer                   $login_times        登录次数
 * @property string                    $reg_ip             注册IP
 * @property string                    $is_enable
 * @property \Carbon\Carbon            $created_at
 * @property \Carbon\Carbon            $updated_at
 * @property string                    $remember_token
 * @property-read PamRoleAccount       $role
 * @property-read Collection|PamRole[] $roles
 * @mixin \Eloquent
 */
class PamAccount extends \Eloquent implements Authenticatable, JWTSubjectAuthenticatable
{

	use TraitAuthenticatable, RbacUserTrait;

	const ACCOUNT_TYPE_BACKEND = 'backend';
	const ACCOUNT_TYPE_USER    = 'user';

	const GUARD_WEB     = 'web';
	const GUARD_BACKEND = 'backend';
	const GUARD_DEVELOP = 'develop';

	const REG_PLATFORM_IOS     = 'ios';
	const REG_PLATFORM_ANDROID = 'android';
	const REG_PLATFORM_WEB     = 'web';
	const REG_PLATFORM_PC      = 'pc';

	const REG_TYPE_USERNAME = 'username';
	const REG_TYPE_MOBILE   = 'mobile';
	const REG_TYPE_EMAIL    = 'email';

	const IS_ENABLE_YES = 1;
	const IS_ENABLE_NO  = 0;


	const ACCOUNT_TYPE_DESKTOP = 'desktop';
	const ACCOUNT_TYPE_FRONT   = 'front';
	const ACCOUNT_TYPE_DEVELOP = 'develop';
	const DESKTOP_SYSTEM       = 'system';  // 后台类型

	protected $table = 'pam_account';

	protected $primaryKey = 'id';

	protected $dates = [
		'logined_at',
	];

	protected $fillable = [
		'mobile',
		'username',
		'password',
		'logined_at',
		'password_key',
		'reg_ip',
	];

	protected static $userTypeDesc = [
		self::ACCOUNT_TYPE_DESKTOP => [
			'name' => '管理员',
			'type' => 'desktop',
			'desc' => '管理员',
		],
		self::ACCOUNT_TYPE_DEVELOP => [
			'name' => '开发者账号',
			'type' => 'develop',
			'desc' => '开发者账号',
		],
		self::ACCOUNT_TYPE_FRONT   => [
			'name' => '用户',
			'type' => 'front',
			'desc' => '前台用户',
		],
		self::DESKTOP_SYSTEM       => [
			'name' => '系统',
			'type' => 'desktop',
			'desc' => '系统后台主动操控',
		],
	];


	/**
	 * 根据账户名称/类型获取账户ID
	 * @param $account_name
	 * @return mixed
	 */
	public static function getAccountIdByAccountName($account_name)
	{
		return self::where('account_name', $account_name)->value('account_id');
	}


	public static function userType($account_id)
	{
		if (!$account_id) {
			return self::DESKTOP_SYSTEM;
		}
		$accountType = self::getAccountTypeByAccountId($account_id);
		return $accountType;
	}


	/**
	 * 用户所有类型
	 * @return array
	 */
	public static function userTypeLinear()
	{
		$linear = [];
		foreach (self::$userTypeDesc as $key => $val) {
			$linear[$key] = $val['name'];
		}
		return $linear;
	}

	/**
	 * 用户账户类型描述
	 * @param $key
	 * @return string
	 */
	public static function userTypeDesc($key)
	{
		static $cache;
		if (!$cache) {
			$cache = self::userTypeLinear();
		}
		return isset($cache[$key]) ? $cache[$key] : '';
	}

	/**
	 * 获取所有账户类型
	 * @return array
	 */
	public static function accountTypeAll()
	{
		$accountTypeDesc = [];
		$keys            = [
			self::ACCOUNT_TYPE_FRONT,
			self::ACCOUNT_TYPE_DESKTOP,
		];

		// 启用系统账号管理
		if (config('lemon.enable_develop')) {
			$keys[] = self::ACCOUNT_TYPE_DEVELOP;
		}
		foreach (self::$userTypeDesc as $key => $val) {
			if (in_array($key, $keys)) {
				$accountTypeDesc[$key] = $val;
			}
		}
		return $accountTypeDesc;
	}


	/**
	 * 允许缓存, 获取账户类型, 因为账户类型不会变化
	 * @param $account_id
	 * @return mixed
	 */
	public static function getAccountTypeByAccountId($account_id)
	{
		static $accountType;
		if (!isset($accountType[$account_id])) {
			$accountType[$account_id] = PamAccount::where('account_id', $account_id)->value('account_type');
		}
		return $accountType[$account_id];
	}

	/**
	 * 账户类型
	 * @return array
	 */
	public static function accountTypeLinear()
	{
		$linear       = [];
		$accountTypes = self::accountTypeAll();
		foreach ($accountTypes as $key => $val) {
			$linear[$key] = $val['name'];
		}
		return $linear;
	}

	/**
	 * 账户类型描述
	 * @param $key
	 * @return string
	 */
	public static function accountTypeDesc($key)
	{
		$linear = self::accountTypeLinear();
		return isset($linear[$key]) ? $linear[$key] : '';
	}


	/**
	 * 绑定社会化组件
	 * @param      $account_id
	 * @param      $field
	 * @param      $key
	 * @param null $head_pic
	 * @return bool
	 */
	public static function bindSocialite($account_id, $field, $key, $head_pic = null)
	{
		if ($head_pic) {
			/* 拖慢性能. 暂时不处理
			$img         = \Image::make($head_pic);
			$destination = 'uploads/avatar/' . $account_id . '.png';
			$img->save(public_path($destination));
			$head_pic = $destination;
			 */
			AccountFront::where('account_id', $account_id)->update([
				'head_pic' => $head_pic,
			]);
		}
		return true;
	}

	/**
	 * 获取定义的 kv 值
	 * @param null|string $key       需要获取的key, 默认返回整个定义
	 * @param bool        $check_key 检测当前key 是否存在
	 * @return array|string
	 */
	public static function kvRegType($key = null, $check_key = false)
	{
		$desc = [
			self::REG_TYPE_USERNAME => '用户名',
			self::REG_TYPE_MOBILE   => '手机号',
			self::REG_TYPE_EMAIL    => '邮箱',
		];
		return kv($desc, $key, $check_key);
	}


	/**
	 * 注册平台
	 * @param null $key
	 * @param bool $check_exists
	 * @return array|string
	 */
	public static function kvRegPlatform($key = null, $check_exists = false)
	{
		$desc = [
			self::REG_PLATFORM_ANDROID => 'android',
			self::REG_PLATFORM_IOS     => 'ios',
			self::REG_PLATFORM_PC      => 'pc',
			self::REG_PLATFORM_WEB     => 'web',
		];
		return kv($desc, $key, $check_exists);
	}

	/**
	 * 账户类型
	 * @param null $key
	 * @param bool $check_exists
	 * @return array|string
	 */
	public static function kvAccountType($key = null, $check_exists = false)
	{

		$desc = [
			self::ACCOUNT_TYPE_BACKEND => '后台',
			self::ACCOUNT_TYPE_USER    => '用户',
		];
		return kv($desc, $key, $check_exists);
	}

	/**
	 * Get the identifier that will be stored in the subject claim of the JWT.
	 * @return mixed
	 */
	public function getJWTIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Return a key value array, containing any custom claims to be added to the JWT.
	 * @return array
	 */
	public function getJWTCustomClaims()
	{
		return [
			'user' => [
				'id' => $this->id,
			],
		];
	}
}

