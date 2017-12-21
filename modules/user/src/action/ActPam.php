<?php namespace User\Action;

/**
 * 基本账户操作
 */

use Poppy\Framework\Helper\EnvHelper;
use Poppy\Framework\Traits\BaseTrait;
use User\Classes\PamHelper;
use Carbon\Carbon;
use User\Models\PamAccount;

class ActPam
{

	use BaseTrait;

	const CRYPT_METHOD = 'AES-256-ECB';

	/** @var  string 隐藏在加密中的字符串 */
	private $hiddenStr;

	/**
	 * 注册基本用户
	 * @param string $account_name
	 * @param string $password
	 * @param string $role_name
	 * @param string $reg_platform
	 * @return bool
	 */
	public function register($account_name, $password, $role_name, $reg_platform = PamAccount::REG_PLATFORM_WEB)
	{
		// validation
		$validator = \Validator::make([
			'account_name' => $account_name,
			'password'     => $password,
		], [
			'account_name' => 'required',
			'password'     => 'required',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}
		if (!$this->isAccountName($account_name)) {
			return $this->setError('用户名格式不正确, 用户名应该为中文, 数字, 字母, 下划线组成');
		}
		if (!PamAccount::kvRegPlatform($reg_platform, true)) {
			return $this->setError('注册平台不存在');
		}


		if (PamAccount::accountNameExists($account_name)) {
			return $this->setError('账号名已存在,不能重复添加');
		}

		// role and account type
		$role = PamRole::where('role_name', $role_name)->first();
		if (!$role) {
			return $this->setError('给定的用户角色不存在');
		}
		$account_type = $role->account_type;

		$salt   = str_random(6);
		$genPwd = PamHelper::genPwd($password, $salt);
		$data   = [
			'account_name' => $account_name,
			'account_key'  => $salt,
			'account_pwd'  => $genPwd,
			'account_type' => $account_type,
			'reg_ip'       => EnvHelper::ip(),
			'reg_platform' => $reg_platform,
		];

		return \DB::transaction(function () use ($role, $role_name, $data) {

			/** @var PamAccount $pam */
			$pam = PamAccount::create($data);
			if (!$pam->account_name) {
				return $this->setError('注册失败, 请联系管理员');
			}
			// 给用户添加角色
			$pam->roles()->attach($role->id);
			$this->setPam($pam);
			return true;
		});
	}


	/**
	 * 删除离线
	 * @throws \Exception
	 */
	public function clearOffline()
	{
		// delete 所有的的超期
		PamAccount::where('logined_at', '<', Carbon::now()->subMinute(config('session.lifetime')))->delete();
	}


	/**
	 * 修改账号的密码, 最小长度 6, 最大长度 20, 原来密码不做验证
	 * @param string $old_password 原密码
	 * @param string $new_password 新密码
	 * @return bool
	 */
	public function changePwd($old_password, $new_password)
	{
		// 基础验证
		if (!$this->checkPam()) {
			return false;
		}

		// 输入验证
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

		// 服务器验证
		if (!$this->checkPassword($old_password)) {
			return $this->setError('原密码错误');
		}

		// do something
		if (!$this->setPassword($new_password)) {
			return false;
		}
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
		$salt                   = str_random(6);
		$genPwd                 = PamHelper::genPwd($password, $salt);
		$this->pam->account_pwd = $genPwd;
		$this->pam->account_key = $salt;
		$this->pam->save();
		return true;
	}

	/**
	 * 检测账户密码是否正确
	 * @param String $password 用户传入的密码
	 * @return bool
	 */
	public function checkPassword($password)
	{
		$salt         = $this->pam->account_key;
		$authPassword = $this->pam->getAuthPassword();
		return (bool) ($authPassword === PamHelper::genPwd($password, $salt));
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


}