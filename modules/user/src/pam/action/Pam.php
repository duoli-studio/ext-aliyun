<?php namespace User\Pam\Action;

use Carbon\Carbon;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\PamRole;
use System\Models\SysConfig;
use User\Models\PamBind;
use User\Models\UserProfile;
use User\Pam\Events\LoginFailed;
use User\Pam\Events\LoginSuccess;
use User\Pam\Events\PamRegistered;
use Util\Util\Action\Util;

class Pam
{
	use SystemTrait;

	/**
	 * @var PamAccount
	 */
	protected $pam;

	/**
	 * @var UserProfile
	 */
	private $userProfile;

	/**
	 * @var string Pam table
	 */
	private $pamTable;

	/**
	 * @var string Bind table
	 */
	private $bindTable;


	public function __construct()
	{
		$this->pamTable    = (new PamAccount())->getTable();
		$this->userProfile = (new UserProfile())->getTable();
		$this->bindTable   = (new PamBind())->getTable();
	}


	/**
	 * 验证验证码
	 * @param $passport
	 * @param $captcha
	 * @return bool
	 * @throws \Throwable
	 */
	public function captchaLogin($passport, $captcha)
	{
		$initDb = [
			'passport' => $passport,
			'captcha'  => $captcha,
		];

		$rule = [
			'captcha' => Rule::required(),
		];

		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$actUtil = app('act.util');
		if (!$actUtil->validCaptcha($passport, $captcha)) {
			return $this->setError($actUtil->getError()->getMessage());
		}

		$passportType = $this->passportType($passport);

		// 判定账号是否存在
		if (!PamAccount::where($passportType, $initDb['passport'])->exists()) {
			if ($this->register($initDb['passport'])) {
				$actUtil->deleteCaptcha($passport, $captcha);
				return true;
			}
			else {
				return false;
			}
		}
		else {
			// 登录
			$this->pam = PamAccount::where($passportType, $passport)->first();
			return true;
		}
	}

	/**
	 * 用户注册
	 * @param string $passport
	 * @param string $password
	 * @param string $role
	 * @return bool
	 * @throws \Throwable
	 */
	public function register($passport, $password = '', $role = PamRole::FE_USER)
	{
		// 组织数据 -> 根据数据库字段来组织
		$passport = strtolower($passport);

		$type = $this->passportType($passport);

		$initDb = [
			$type      => strval($passport),
			'password' => strval($password),
		];

		$rule = [
			$type      => [
				Rule::required(),
				Rule::string(),
				Rule::between(6, 30),
				// 唯一性认证
				Rule::unique($this->pamTable, $type),
			],
			'password' => [
				Rule::string(),
			],
		];

		// 完善主账号类型规则
		if ($type == PamAccount::REG_TYPE_MOBILE) {
			$rule[$type][] = Rule::mobile();
		}
		elseif ($type == PamAccount::REG_TYPE_EMAIL) {
			$rule[$type][] = Rule::email();
		}
		else {
			if (preg_match('/\s+/', $passport)) {
				return $this->setError('用户名中不得包含空格');
			}
			$rule[$type][] = 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u';
		}

		// 密码不为空时候的检测
		if ($password !== '') {
			$rule['password'] += [
				Rule::between(6, 16),
				Rule::required(),
				Rule::password(),
			];
		}

		// 验证数据
		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// 服务器处理
		/** @var PamRole $role */
		$role = PamRole::where('name', $role)->first();
		if (!$role) {
			return $this->setError('给定的用户角色不存在');
		}

		// 自动设置前缀
		$prefix = strtoupper(strtolower($this->getSetting()->get('system::site.account_prefix')));
		if ($type != PamAccount::REG_TYPE_USERNAME) {
			$hasAccountName = false;
			// 检查是否设置了前缀
			if (!$prefix) {
				return $this->setError('尚未设置用户名默认前缀, 无法注册, 请联系管理员!');
			}
			$username = $prefix . '_' . Carbon::now()->format('YmdHis') . str_random(6);
		}
		else {
			$hasAccountName = true;
			$username       = $passport;
		}

		$initDb['username']  = $username;
		$initDb['type']      = $role->type;
		$initDb['is_enable'] = SysConfig::YES;

		try {
			// 处理数据库
			return \DB::transaction(function () use ($initDb, $role, $password, $hasAccountName, $prefix) {

				/** @var PamAccount $pam pam */
				$pam = PamAccount::create($initDb);

				// 给用户默认角色
				$pam->roles()->attach($role->id);

				// 如果没有设置账号, 则根据规范生成用户名
				if (!$hasAccountName) {
					$formatAccountName = sprintf("%s_%'.09d", $prefix, $pam->id);
					$pam->username     = $formatAccountName;
					$pam->save();
				}

				if ($password) {
					// 设置密码
					$this->setPassword($pam, $password);
				}

				// 触发注册成功的事件
				$this->getEvent()->dispatch(new PamRegistered($pam));

				$this->pam = $pam;
				return true;
			});
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 密码登录
	 * @param $passport
	 * @param $password
	 * @return bool
	 */
	public function loginPwd($passport, $password)
	{
		$passport = strtolower($passport);
		$type     = $this->passportType($passport);
		$initDb   = [
			$type      => strval($passport),
			'password' => strval($password),
		];
		$rule     = [
			$type      => [
				Rule::required(),
				Rule::between(6, 18),
				Rule::string(),
			],
			'password' => [
				Rule::required(),
				Rule::between(6, 18),
			],
		];
		if ($type == PamAccount::REG_TYPE_MOBILE) {
			$rule[$type][] = Rule::mobile();
		}
		elseif ($type == PamAccount::REG_TYPE_EMAIL) {
			$rule[$type][] = Rule::email();
		}
		else {
			if (preg_match('/\s+/', $passport)) {
				return $this->setError('用户名中不得包含空格');
			}
			$rule[$type][] = 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u';
		}
		// 验证数据
		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$initType = $initDb[$type];
		//判断此用户是否注册过
		$pam = PamAccount::where(function ($query) use ($initType) {
			$query->where('username', $initType)
				->orwhere('email', $initType)
				->orwhere('mobile', $initType);
		})->get();

		if ($pam->isEmpty()) {
			return $this->setError('无此用户，请先去注册');
		}
		//如果没注册过　去请求　captchaRegister  这个接口　然后请求　register 接口　去设置基本信息
		foreach ($pam as $p) {
			$result = $this->checkPassword($p, $initDb['password']);
		}
		if (!$result) {
			return $this->setError('密码或用户名不对');
		}
		return true;
	}


	/**
	 * 用户注册
	 * @param string $passport
	 * @param string $password
	 * @param string $role
	 * @return bool
	 * @throws \Throwable
	 */
	public function registerBak($passport, $password, $nickname, $sex, $role = PamRole::FE_USER)
	{
		// 组织数据 -> 根据数据库字段来组织
		$passport = strtolower($passport);

		$type = $this->passportType($passport);

		$initDb = [
			$type      => strval($passport),
			'password' => strval($password),
		];

		$rule = [
			$type      => [
				Rule::required(),
				Rule::string(),
				Rule::between(6, 30),
				// 唯一性认证
				Rule::unique($this->pamTable, $type),
			],
			'password' => [
				Rule::required(),
				Rule::password(),
				Rule::string(),
				Rule::between(6, 16),
			],
		];
		if ($type == PamAccount::REG_TYPE_MOBILE) {
			$rule[$type][] = Rule::mobile();
		}
		elseif ($type == PamAccount::REG_TYPE_EMAIL) {
			$rule[$type][] = Rule::email();
		}
		else {
			if (preg_match('/\s+/', $passport)) {
				return $this->setError('用户名中不得包含空格');
			}
			$rule[$type][] = 'regex:/[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9_\x{4e00}-\x{9fa5}]/u';
		}
		// 验证数据
		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		// 服务器处理
		// role and account type
		$role = PamRole::where('name', $role)->first();
		if (!$role) {
			return $this->setError('给定的用户角色不存在');
		}
		// 自动设置前缀
		$prefix = strtoupper(strtolower($this->getSetting()->get('system::site.account_prefix')));
		if ($type != PamAccount::REG_TYPE_USERNAME) {
			$hasAccountName = false;
			// 检查是否设置了前缀
			if (!$prefix) {
				return $this->setError('尚未设置用户名默认前缀, 无法注册, 请联系管理员!');
			}
			$username = $prefix . '_' . Carbon::now()->format('YmdHis') . str_random(6);
		}
		else {
			$hasAccountName = true;
			$username       = $passport;
		}

		$initDb['username']  = $username;
		$initDb['type']      = $role->type;
		$initDb['is_enable'] = SysConfig::YES;

		try {
			// 处理数据库
			return \DB::transaction(function () use ($initDb, $role, $hasAccountName, $prefix, $nickname, $sex) {
				/** @var PamAccount $pam pam account */
				$pam = PamAccount::create($initDb);

				$user   = new User();
				$result = $user->register($nickname, $sex, $pam->id);
				if (!$result) {
					throw new \Exception($user->getError());
				}
				// 给用户默认角色
				$pam->roles()->attach($role->id);
				// 如果没有设置账号, 则根据规范生成用户名
				if (!$hasAccountName) {
					$formatAccountName = sprintf("%s_%'.09d", $prefix, $pam->id);
					$pam->username     = $formatAccountName;
					$pam->save();
				}
				// 设置密码
				$this->setPassword($pam, $pam->password);
				$this->pam = $pam;
				// 触发注册成功的事件
				$this->getEvent()->dispatch(new PamRegistered($pam));
				return true;
			});
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}


	/**
	 * 检查登录是否成功
	 * @param string $passport
	 * @param string $password
	 * @param string $guard_type
	 * @param bool   $remember
	 * @return bool
	 */
	public function loginCheck($passport, $password, $guard_type = PamAccount::GUARD_WEB, $remember = false)
	{
		$pamTable = (new PamAccount())->getTable();

		$type        = $this->passportType($passport);
		$credentials = [
			$type      => $passport,
			'password' => $password,
		];

		// check exists
		$validator = \Validator::make($credentials, [
			$type      => [
				Rule::required(),
				Rule::exists($pamTable, $type),
			],
			'password' => Rule::required(),
		], []);
		if ($validator->fails()) {
			return $this->setError($validator->errors());
		}

		/** @var \Auth $guard */
		$guard = \Auth::guard($guard_type);
		// dd($remember);
		// dd($credentials);
		if ($guard->attempt($credentials, $remember)) {
			/** @var PamAccount $user */
			$user = $guard->user();

			if ($user->is_enable == SysConfig::NO) {
				// 账户被禁用
				$guard->logout();
				return $this->setError('本账户被禁用, 不得登入');
			}

			$this->getEvent()->dispatch(new LoginSuccess($user));
			$this->pam = $user;
			return true;
		}
		else {
			$credentials += [
				'type'     => $type,
				'passport' => $passport,
			];
			$this->getEvent()->dispatch(new LoginFailed($credentials));
			return $this->setError('登录失败, 请重试');
		}

	}

	/**
	 * 绑定qq wx　第三方登录
	 * @param $type
	 * @return bool
	 */
	public function bind($type)
	{
		$validator = \Validator::make([
			'type' => $type,
		], [
			'type' => [
				Rule::required(),
				Rule::in('qq', 'alipay', 'weixin'),
			],
		], [], [
			'type' => '第三方绑定账号类型',
		]);

		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$initDb = [
			'qq_key'      => strval(array_get($type, 'qq_key')),
			'wx_key'      => strval(array_get($type, 'wx_key')),
			'wx_union_id' => strval(array_get($type, 'wx_union_id')),
			'qq_union_id' => strval(array_get($type, 'qq_union_id')),
		];
		//检测他在bind 表中是否存在　

		//不存在　即表示　他是第一次进入平台　让他去访问那个

		PamBind::create($initDb);
	}

	/**
	 * 找回密码->发送验证码
	 * @param $passport
	 * @return bool
	 */
	public function RecoverPassword($passport)
	{
		//接收数据，判断数据是手机号还是邮箱或者用户名
		$passport = strtolower($passport);
		$type     = $this->passportType($passport);
		$initDb   = [
			$type => strval($passport),
		];
		$initType = $initDb[$type];
		//判断此用户是否注册过
		$result = PamAccount::where(function ($query) use ($initType) {
			$query->where('username', $initType)
				->orwhere('email', $initType)
				->orwhere('mobile', $initType);
		})->get();

		if ($result->isEmpty()) {
			return $this->setError('该用户没有注册');
		}
		//生成验证码并且发送
		switch ($type) {
			case 'mobile':
				$util   = new Util();
				$result = $util->sendCaptcha($initType);
				break;
			case 'email':
				//调用第三方邮箱发送
				break;
		}
		if (!$result) {
			return $this->setError('验证码发送失败');
		}
		//验证用户输入的验证码是否正确  请求那个validatorCaptcha
		return true;
	}

	/**
	 * 找回密码->修改密码
	 * @param $passport
	 * @param $password
	 * @return bool
	 */
	public function findPassword($passport, $password)
	{
		$password  = strval($password);
		$initDb    = [
			'password' => $password,
		];
		$rule      = [
			'password' => [
				Rule::required(),
				Rule::password(),
				Rule::string(),
				Rule::between(6, 16),
			],
		];
		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		$newPassword = array_get($initDb, 'password');
		$pam         = PamAccount::where('mobile', $passport)->get();
		foreach ($pam as $p) {
			$result = $this->setPassword($p, $newPassword);
		}
		if (!$result) {
			return $this->setError('修改密码失败');
		}
		return true;
	}

	/**
	 * 密码重置
	 * @param $account_id
	 * @param $oldPassword
	 * @param $newPassword
	 * @return bool
	 */
	public function resetPassword($account_id, $oldPassword, $newPassword)
	{
		$oldPassword = strval($oldPassword);
		$newPassword = strval($newPassword);
		$pam         = PamAccount::where('id', $account_id)->get();
		foreach ($pam as $p) {
			$result = $this->checkPassword($p, $oldPassword);
		}

		if (!$result) {
			return $this->setError('原密码不对，请重新输入');
		}

		foreach ($pam as $p) {
			$ok = $this->setPassword($p, $newPassword);
		}
		if (!$ok) {
			return $this->setError('修改密码成功');
		}
		return true;
	}

	/**
	 * 退出
	 * @return bool
	 */
	public function webLogout()
	{
		\Auth::guard(PamAccount::GUARD_WEB)->logout();
		return true;
	}

	/**
	 * 设置登录密码
	 * @param PamAccount $pam
	 * @param string     $password
	 * @return bool
	 */
	public function setPassword($pam, $password)
	{
		$validator = \Validator::make([
			'password' => $password,
		], [
			'password' => 'required|between:6,20',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$key               = str_random(6);
		$regDatetime       = $pam->created_at->toDateTimeString();
		$cryptPassword     = $this->cryptPassword($password, $regDatetime, $key);
		$pam->password     = $cryptPassword;
		$pam->password_key = $key;
		$pam->save();
		return true;
	}

	/**
	 * 检测账户密码是否正确
	 * @param PamAccount $pam      用户账户信息
	 * @param String     $password 用户传入的密码
	 * @return bool
	 */
	public function checkPassword($pam, $password)
	{
		$key           = $pam->password_key;
		$reg_datetime  = $pam->created_at->toDateTimeString();
		$authPassword  = $pam->getAuthPassword();
		$cryptPassword = $this->cryptPassword($password, $reg_datetime, $key);
		return (bool) ($authPassword === $cryptPassword);
	}

	/**
	 * 生成账户密码
	 * @param String $password     原始密码
	 * @param String $reg_datetime 注册时间(datetime) 类型
	 * @param String $random_key   六位随机值
	 * @return string
	 */
	private function cryptPassword($password, $reg_datetime, $random_key)
	{
		return md5(sha1($password . $reg_datetime) . $random_key);
	}

	/**
	 * Passport Type
	 * @param $passport
	 * @return string
	 */
	private function passportType($passport)
	{
		if (UtilHelper::isMobile($passport)) {
			$type = PamAccount::REG_TYPE_MOBILE;
		}
		elseif (UtilHelper::isEmail($passport)) {
			$type = PamAccount::REG_TYPE_EMAIL;
		}
		else {
			$type = PamAccount::REG_TYPE_USERNAME;
		}
		return $type;
	}


}