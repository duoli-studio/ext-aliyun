<?php namespace System\Pam\Action;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\PamBind;
use System\Models\PamCaptcha;
use System\Models\PamRole;
use System\Models\SysConfig;
use System\Pam\Events\LoginFailed;
use System\Pam\Events\LoginSuccess;
use System\Pam\Events\PamRegistered;
use Tymon\JWTAuth\JWTGuard;
use System\Captcha\Action\Captcha;

class Pam
{
	use SystemTrait;

	/**
	 * @var PamAccount
	 */
	protected $pam;

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
		$this->pamTable  = (new PamAccount())->getTable();
		$this->bindTable = (new PamBind())->getTable();
	}


	/**
	 * 验证验登录
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

		$actUtil = new Captcha();
		if (!$actUtil->check($passport, $captcha)) {
			return $this->setError($actUtil->getError()->getMessage());
		}

		$passportType = $this->passportType($passport);

		// 判定账号是否存在
		if (!PamAccount::where($passportType, $initDb['passport'])->exists()) {
			if ($this->register($initDb['passport'])) {
				$actUtil->delete($passport);
				return true;
			}
			else {
				return false;
			}
		}
		else {
			// 登录
			$this->pam = PamAccount::where($passportType, $passport)->first();

			if ($this->pam->is_enable == SysConfig::NO) {
				// 账户被禁用
				$this->webLogout();
				return $this->setError('本账户被禁用, 不得登入');
			}
			//登录时间
			$this->pam->logined_at  = Carbon::now();
			$this->pam->login_times += 1;
			$this->pam->save();
			return true;
		}
	}

	/**
	 * 用户注册
	 * @param string $passport
	 * @param string $password
	 * @param string $role_name
	 * @return bool
	 * @throws \Throwable
	 */
	public function register($passport, $password = '', $role_name = PamRole::FE_USER)
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

		/** @var PamRole $role */
		$role = PamRole::where('name', $role_name)->first();
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
			return \DB::transaction(function() use ($initDb, $role, $password, $hasAccountName, $prefix) {

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

		if ($guard->attempt($credentials, $remember)) {
			// jwt 不能获取到 user， 使用 getLastAttempted 方法来获取数据
			if ($guard instanceof JWTGuard) {
				/** @var PamAccount $user */
				$user = $guard->getLastAttempted();
			}
			else {
				/** @var PamAccount $user */
				$user = $guard->user();
			}

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
	 * 找回手机号
	 * @param $passport
	 * @return bool
	 */
	public function findMobile($passport)
	{
		$type   = $this->passportType($passport);
		$initDb = [
			$type => strval($passport),
		];
		$rule   = [
			Rule::required(),
			Rule::exists($this->pamTable, $type),
			Rule::string(),
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

		$validator = \Validator::make($initDb, $rule);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}


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
	 * 新手机号发送验证码
	 * @param $verify_code
	 * @param $newMobile
	 * @return bool
	 */
	public function newSendCaptcha($verify_code, $newMobile)
	{
		$data      = [
			'verify_code' => $verify_code,
			'mobile'      => $newMobile,
		];
		$validator = \Validator::make($data, [
			'verify_code' => [
				Rule::required(),
				Rule::string(),
			],
			'mobile'      => [
				Rule::required(),
				Rule::mobile(),
			], [], [
				'verify_code' => trans('system::bind_change.db.verify_code'),
				'mobile'      => trans('system::bind_change.db.mobile'),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		$actUtil = new Captcha();
		if (!$actUtil->verifyOnceCode($verify_code)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		//验证新手机号是否已经注册
		if (PamAccount::where('mobile', $newMobile)->exists()) {
			return $this->setError('该手机号已经注册过');
		}

		//发送验证码
		$actUtil->send($newMobile, $type = PamCaptcha::CON_LOGIN);
		return true;
	}

	/**
	 * 新的手机号验证
	 * @param $verify_code
	 * @param $new_passport
	 * @param $captcha
	 * @return bool
	 * @throws \Exception
	 */
	public function rebindPassport($verify_code, $new_passport, $captcha)
	{
		if (!$this->checkPam()) {
			return false;
		}

		$data      = [
			'verify_code' => $verify_code,
			'mobile'      => $new_passport,
			'captcha'     => $captcha,
		];
		$validator = \Validator::make($data, [
			'verify_code' => [
				Rule::required(),
				Rule::string(),
			],
			'mobile'      => [
				Rule::required(),
				Rule::mobile(),
			],
			'captcha'     => [
				Rule::required(),
			], [], [
				'verify_code' => trans('system::bind_change.db.verify_code'),
				'mobile'      => trans('system::bind_change.db.mobile'),
				'captcha'     => trans('system::bind_change.db.captcha'),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// 验证一次码
		$Captcha = new Captcha();
		if (!$Captcha->verifyOnceCode($verify_code)) {
			return $this->setError($Captcha->getError()->getMessage());
		}

		// 验证验证码
		if (!$Captcha->check($new_passport, $captcha)) {
			return $this->setError($Captcha->getError()->getMessage());
		}
		$Captcha->delete($new_passport);

		try {
			$this->pam->update([
				'mobile' => $new_passport,
			]);
			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
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
	 * 生成支持 passport 格式的数组
	 * @param $credentials
	 * @return array
	 */
	public function passportData($credentials)
	{
		if ($credentials instanceof Request) {
			$credentials = $credentials->all();
		}
		$passport     = $credentials['passport'] ?? '';
		$passport     = $passport ?: $credentials['mobile'] ?? '';
		$passport     = $passport ?: $credentials['username'] ?? '';
		$passport     = $passport ?: $credentials['email'] ?? '';
		$passportType = $this->passportType($passport);
		return [
			$passportType => $passport,
			'password'    => $credentials['password'] ?? '',
		];
	}

	/**
	 * Passport Type
	 * @param $passport
	 * @return string
	 */
	public function passportType($passport)
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

	/**
	 * 后台用户禁用
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	public function disable($id, $data)
	{
		$data      = json_decode($data, true);
		$data      = [
			'disable_reason' => strval(array_get($data, 'disable_reason', '')),
			'disable_to'     => strval(array_get($data, 'disable_to', date("Y-m-d",strtotime('+3 days')))),
		];
		$validator = \Validator::make($data, [
			'disable_reason' => [
				Rule::string(),
			],
			'disable_to'     => [
				Rule::string(),
				Rule::dateFormat('Y-m-d'),
			], [], [
				'disable_reason' => trans('system::account.action.disable_reason'),
				'disable_to'     => trans('system::account.action.disable_to'),
			],
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		if (!PamAccount::where('id', $id)->where('is_enable', 1)->exists()) {
			//当前用户已禁用
			return $this->setError('当前用户已禁用');
		}
		try {
			PamAccount::where('id', $id)->update([
				'is_enable'        => PamAccount::STATUS_DISABLE,
				'disable_reason'   => $data['disable_reason'],
				'disable_start_at' => Carbon::now(),
				'disable_end_at'   => Carbon::now()->addMinute($data['disable_to']),
			]);
			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}

	}

	/**
	 * 后台用户启用
	 * @param $id
	 * @return bool
	 */
	public function enable($id)
	{
		if (PamAccount::where('id', $id)->where('is_enable', 1)->exists()) {
			return $this->setError('当前用户为启用状态');
		}
		try {
			PamAccount::where('id', $id)->update([
				'is_enable' => PamAccount::STATUS_ENABLE,
			]);
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}

	}

	/**
	 * 自动解禁
	 * @return bool
	 */
	public function autoDisable()
	{
		try {
			PamAccount::where('disable_end_at', '<', Carbon::now())->update([
				'is_enable' => PamAccount::STATUS_ENABLE,
			]);
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}

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


}