<?php namespace System\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable as TraitAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Poppy\Framework\Http\Pagination\PageInfo;
use System\Action\Pam;
use System\Classes\Traits\FilterTrait;
use System\Rbac\Traits\RbacUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubjectAuthenticatable;

/**
 * System\Models\PamAccount
 *
 * @property int                       $id
 * @property string                    $mobile             手机号
 * @property string                    $username           用户名称
 * @property string                    $password           用户密码
 * @property Carbon                    $logined_at         注册时间
 * @property string                    $account_name       账号名称， 支持中文
 * @property string                    $account_pwd        账号密码
 * @property string                    $account_key        账号注册时候随机生成的6位key
 * @property string                    $account_type       账户类型
 * @property int                   $login_times        登录次数
 * @property string                    $reg_ip             注册IP
 * @property string                    $is_enable
 * @property Carbon                    $created_at
 * @property Carbon                    $updated_at
 * @property string                    $remember_token
 * @property-read PamRoleAccount       $role
 * @property-read Collection|PamRole[] $roles
 * @mixin \Eloquent
 * @property string|null               $email              邮箱
 * @property string|null               $password_key       账号注册时候随机生成的6位key
 * @property string|null               $type               邮箱
 * @property string|null               $reg_platform       注册平台
 * @property string                    $disable_reason     禁用原因
 * @property string|null               $disable_start_at   禁用开始时间
 * @property string|null               $disable_end_at     禁用结束时间
 * @method static Builder|PamAccount filter($input = [], $filter = null)
 * @method static Builder|PamAccount pageFilter(PageInfo $pageInfo)
 * @method static Builder|PamAccount paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|PamAccount simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page')
 * @method static Builder|PamAccount whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|PamAccount whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|PamAccount whereLike($column, $value, $boolean = 'and')
 */
class PamAccount extends \Eloquent implements Authenticatable, JWTSubjectAuthenticatable
{
	use TraitAuthenticatable, RbacUserTrait, Authorizable, FilterTrait;

	/* Register Type
	 -------------------------------------------- */
	const TYPE_BACKEND = 'backend';
	const TYPE_USER    = 'user';
	const TYPE_DEVELOP = 'develop';

	/* Register By
	 -------------------------------------------- */
	const REG_TYPE_USERNAME = 'username';
	const REG_TYPE_MOBILE   = 'mobile';
	const REG_TYPE_EMAIL    = 'email';

	/* Guard Type
	 -------------------------------------------- */
	const GUARD_WEB         = 'web';
	const GUARD_BACKEND     = 'backend';
	const GUARD_DEVELOP     = 'develop';
	const GUARD_JWT_BACKEND = 'jwt_backend';
	const GUARD_JWT_WEB     = 'jwt_web';

	/* Register Platform
	 -------------------------------------------- */
	const REG_PLATFORM_IOS     = 'ios';
	const REG_PLATFORM_ANDROID = 'android';
	const REG_PLATFORM_WEB     = 'web';
	const REG_PLATFORM_PC      = 'pc';

	const STATUS_ENABLE  = 1;
	const STATUS_DISABLE = 0;

	protected $table = 'pam_account';

	protected $dates = [
		'logined_at',
	];

	protected $fillable = [
		'mobile',
		'email',
		'username',
		'password',
		'type',
		'logined_at',
		'is_enable',
		'password_key',
		'reg_ip',

		'disable_reason',
		'disable_start_at',
		'disable_end_at',
	];

	public static function passport($passport)
	{
		$type = (new Pam())->passportType($passport);

		return self::where($type, $passport)->first();
	}

	/**
	 * 根据 Username 获取账户ID
	 * @param string $username
	 * @return mixed
	 */
	public static function getIdByUsername($username)
	{
		return self::where('username', $username)->value('id');
	}

	/**
	 * 获取定义的 kv 值
	 * @param null|string $key       需要获取的key, 默认返回整个定义
	 * @param bool        $check_key 检测当前key 是否存在
	 * @return array|string
	 */
	public static function kvType($key = null, $check_key = false)
	{
		$desc = [
			self::TYPE_USER    => '用户',
			self::TYPE_BACKEND => '后台管理员',
			self::TYPE_DEVELOP => '开发者',
		];

		return kv($desc, $key, $check_key);
	}

	/**
	 * 允许缓存, 获取账户类型, 因为账户类型不会变化
	 * @param $id
	 * @return mixed
	 */
	public static function getTypeById($id)
	{
		static $accountType;
		if (!isset($accountType[$id])) {
			$accountType[$id] = self::where('id', $id)->value('type');
		}

		return $accountType[$id];
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