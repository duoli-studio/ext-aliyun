<?php namespace Slt\Action;

/**
 * 基本账户操作
 */

use Carbon\Carbon;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Traits\BaseTrait;
use Illuminate\Validation\Rule;
use User\Models\PamAccount;
use User\Models\PamOnline;
use User\Classes\PamUtil;

class ActAccount
{

	use BaseTrait;

	const CRYPT_METHOD = 'AES-256-ECB';

	/** @var  string 隐藏在加密中的字符串 */
	private $hiddenStr;

	private $guard;


	/**
	 * 注册基本用户
	 * @param string $type
	 * @param string $account      注册账号, 可以为空, 空的话自动生成账号
	 * @param string $password     密码
	 * @param string $account_type 账号类型
	 * @param int    $role_id      角色ID
	 * @param string $reg_platform 注册平台
	 * @return bool
	 * @internal param string $account_name
	 */
	public function register($type, $account, $password, $account_type, $role_id, $reg_platform = PamAccount::REG_PLATFORM_WEB)
	{
		$account = strtolower($account);
		// 传入数据校验
		if (!PamAccount::kvRegType($type, true)) {
			return $this->setError('注册类型不正确');
		}
		$pamTable  = (new PamAccount())->getTable();
		$roleTable = (new PamRole())->getTable();
		$rule      = [
			$type      => [
				'required',
				'string',
				'between:6,30',
				// 唯一性认证
				Rule::unique($pamTable, $type),
			],
			'password' => [
				'string', 'between:6,20',
			],
			'role_id'  => [
				Rule::exists($roleTable, 'id'),
			],
		];
		$data      = [
			'role_id'  => $role_id,
			'password' => $password,
			$type      => $account,
		];
		switch ($type) {
			case PamAccount::REG_TYPE_MOBILE;
				$rule[$type][] = 'mobile';
				break;
			case PamAccount::REG_TYPE_EMAIL;
				$rule[$type][] = 'email';
				break;
			case PamAccount::REG_TYPE_ACCOUNT;
			default:
				if (preg_match('/\s+/', $account)) {
					return $this->setError('用户名中不得包含空格');
				}
				$rule[$type][] = 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u';
				break;
		}

		$validator = \Validator::make($data, $rule, [], array_merge(PamAccount::kvRegType(), [
			'password' => '密码',
			'role_id'  => '用户角色',
		]));

		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$prefix = strtoupper(strtolower(conf('backend::site.account_prefix')));
		if ($type != PamAccount::REG_TYPE_ACCOUNT) {
			$hasAccountName = false;
			// 检查是否设置了前缀
			if (!$prefix) {
				return $this->setError('尚未设置用户名默认前缀, 无法注册, 请联系管理员!');
			}
			$account_name = $prefix . '_' . Carbon::now()->format('YmdHis') . str_random(6);
		}
		else {
			$hasAccountName = true;
			$account_name   = $account;
		}

		if (!PamAccount::kvAccountType($account_type, true)) {
			return $this->setError('账户类型不不存在');
		}

		if (!PamAccount::kvRegPlatform($reg_platform, true)) {
			return $this->setError('注册平台不存在');
		}
		$key = str_random(6);

		$data = [
			'account_name' => $account_name,
			'account_key'  => $key,
			'account_type' => $account_type,
			'reg_ip'       => \Input::getClientIp(),
			'reg_platform' => $reg_platform,
			'account_pwd'  => PamUtil::genPwd($password, $key),
			$type          => $account,
		];

		/** @var PamAccount $pam */
		$pam = PamAccount::create($data);

		if (!$pam->id) {
			return $this->setError('注册失败, 请联系管理员');
		}

		// 如果没有设置账号, 则根据规范生成用户名
		if (!$hasAccountName) {
			$formatAccountName = sprintf("%s_%'.09d", $prefix, $pam->id);
			$pam->account_name = $formatAccountName;
			$pam->save();
		}

		// 给用户添加角色
		$pam->roles()->attach($role_id);
		$this->pam = $pam;

		return true;
	}

	public function setGuard($guard)
	{
		return $this->guard = $guard;
	}


	public function webLogin($passport, $password, $account_type, $remember = true)
	{
		if ($this->loginCheck($passport, $password, $account_type, $remember)) {

			// 更新用户在线信息
			$this->online(PamAccount::REG_PLATFORM_WEB);

			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * 检查登录是否成功
	 * @param string $passport
	 * @param string $password
	 * @param string $account_type
	 * @param bool   $remember
	 * @return bool
	 */
	public function loginCheck($passport, $password, $account_type, $remember = false)
	{
		if (!$this->guard) {
			return $this->setError('请设置运行的环境, 前台还是后台');
		}

		$pamTable = (new PamAccount())->getTable();

		if (UtilHelper::isMobile($passport)) {
			$type = PamAccount::REG_TYPE_MOBILE;
		}
		elseif (UtilHelper::isEmail($passport)) {
			$type = PamAccount::REG_TYPE_EMAIL;
		}
		else {
			$type = PamAccount::REG_TYPE_ACCOUNT;
		}

		$credentials = [
			$type          => $passport,
			'password'     => $password,
			'account_type' => $account_type,
		];

		// check exists
		$validator = \Validator::make($credentials, [
			$type      => [
				'required',
				Rule::exists($pamTable, $type),
			],
			'password' => 'required',
		], [], PamAccount::kvAccountType());
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}
		// login and redirect

		/** @var \Auth $guard */
		$guard = \Auth::guard(PamAccount::GUARD_WEB);
		if ($guard->attempt($credentials, $remember)) {
			/** @var PamAccount $user */
			$user = $guard->user();
			if ($user->is_enable == 'N') {
				// 账户被禁用
				$guard->logout();
				return $this->setError('本账户被禁用, 不得登入');
			}

			$this->pam = $user;

			return true;
		}
		else {
			\Event::fire('auth.failed', [$credentials]);
			return $this->setError('登录失败, 请重试');
		}
	}

	public function webLogout()
	{
		$guard = \Auth::guard(PamAccount::GUARD_WEB);
		$this->offline(PamAccount::REG_PLATFORM_WEB);
		$guard->logout();
	}

	/**
	 * 删除离线
	 * @throws \Exception
	 */
	public function clearOffline()
	{
		// delete 所有的的超期
		PamOnline::where('logined_at', '<', Carbon::now()->subMinute(config('session.lifetime')))->delete();
	}


	/**
	 * 更新用户 ip 和 最后登录时间
	 * @param string $platform 支持的平台
	 */
	private function online($platform)
	{
		PamOnline::updateOrCreate([
			'account_id' => $this->pam->id,
			'platform'   => $platform,
		], [
			'login_ip'   => \Request::ip(),
			'logined_at' => Carbon::now()->toDateTimeString(),
		]);
	}

	/**
	 * 下线
	 * @param $platform
	 */
	private function offline($platform)
	{
		PamOnline::where('account_id', $this->pam->id)
			->where('platform', $platform)
			->delete();
	}



	/**
	 * 修改账号的密码, 最小长度 6, 最大长度 20, 原来密码不做验证
	 * @param string $old_password 原密码
	 * @param string $new_password 新密码
	 * @return bool
	 */
	public function changePassword($old_password, $new_password)
	{
		if (!$this->checkPam()) {
			return false;
		}
		$validator = \Validator::make([
			'old_password' => $old_password,
			'new_password' => $new_password,
		], [
			'old_password' => 'required',
			'new_password' => 'required|between:6,20',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if ($old_password == $new_password) {
			return $this->setError('新输入的密码和原密码不能相同');
		}

		if (!PamAccount::checkPassword($this->pam, $old_password)) {
			return $this->setError('原密码错误');
		}
		PamAccount::changePassword($this->pam, $new_password);
		return true;
	}

	/**
	 * 设置登录密码
	 * @param $password
	 * @return bool
	 */
	public function setPassword($password)
	{
		if (!$this->checkPam()) {
			return false;
		}
		$validator = \Validator::make([
			'password' => $password,
		], [
			'password' => 'required|between:6,20',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		PamAccount::changePassword($this->pam, $password);
		return true;
	}


	/**
	 * 账户名是否合法, 包含:, 允许是中文, 英文, 数字, 下划线, 和英文的 :
	 * @param $account_name
	 * @return int
	 */
	private function isAccountName($account_name)
	{
		$re = "/^[\\x{4e00}-\\x{9fa5}A-Za-z0-9_:]+$/u";
		return preg_match($re, $account_name);
	}

	/**
	 * 需要验证的验证码
	 * @param $verify_code
	 * @return bool
	 */
	public function verifyOnceCode($verify_code)
	{
		$str = openssl_decrypt($verify_code, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
		if (strpos($str, '|') !== false) {
			$split    = explode('|', $str);
			$expire   = (int) $split[0];
			$key      = strval($split[1]);
			$cacheKey = cache_name(__CLASS__, 'once_verify_code');
			$data     = (array) \Cache::get($cacheKey);
			if ($expire < Carbon::now()->timestamp) {
				return $this->setError('安全校验已经过期, 请重新请求');
			}
			// dd($data[$key]);
			if (!isset($data[$key])) {
				return $this->setError('安全校验已经过期, 请重新请求');
			}
			else {
				unset($data[$key]);
				$this->hiddenStr = $split[2];
				\Cache::forever($cacheKey, $data);
				return true;
			}
		}
		else {
			return $this->setError('非法请求');
		}
	}


	/**
	 * 生成一次验证码
	 * @param int    $expired    过期时间
	 * @param string $hidden_str 隐藏的验证字串
	 * @return string
	 */
	public function genOnceVerifyCode($expired = 10, $hidden_str = '')
	{
		// 生成 10 分钟的有效 code
		$now     = Carbon::now();
		$unix    = Carbon::now()->addMinutes($expired)->timestamp;
		$randStr = str_random(16);
		$key     = $now->timestamp . '_' . str_random(6);
		if (!$hidden_str) {
			$hidden_str = str_random(6);
		}
		$str      = $unix . '|' . $key . '|' . $hidden_str . '|' . $randStr;
		$cacheKey = cache_name(__CLASS__, 'once_verify_code');
		if (\Cache::has($cacheKey)) {
			$data = \Cache::get($cacheKey);
			if (is_array($data)) {
				foreach ($data as $item) {
					list($_unix, $_key) = explode('|', $item);
					if ($_unix < $now->timestamp) {
						// key 已经过期, 移除
						unset($data[$_key]);
					}
				}
			}
			$data[$key] = $str;
		}
		else {
			$data = [
				$key => $str,
			];
		}
		\Cache::forever($cacheKey, $data);
		return openssl_encrypt($str, self::CRYPT_METHOD, substr(config('app.key'), 0, 32));
	}

	/**
	 * 获取隐藏的账号数据
	 * @return string
	 */
	public function getHiddenStr()
	{
		return $this->hiddenStr;
	}


	/**
	 * 解绑第三方账号
	 * @param string $type 第三方账号的类型, 暂时只支持 qq
	 * @return bool
	 */
	public function unbind($type)
	{
		if (!$this->checkPam()) {
			return false;
		}

		$validator = \Validator::make([
			'type' => $type,
		], [
			'type' => 'required|in:qq,alipay,weixin',
		], [], [
			'type' => '第三方绑定账号类型',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if ($type == 'qq') {
			PamBind::where('account_id', $this->pam->id)->update([
				'qq_key'      => '',
				'qq_union_id' => '',
			]);
			return true;
		}
		return $this->setError('第三方绑定账号类型错误');
	}
}