<?php namespace Slt\Action;

/**
 * 基本用户操作, 涉及到 web 用户
 * @author     Mark <zhaody901@126.com>
 * @copyright  Copyright (c) 2013-2017 Sour Lemon Team
 */

use Carbon\Carbon;
use Poppy\Framework\Traits\BaseTrait;
use User\Models\PamAccount;
use User\Models\PamRole;

class ActUser
{

	use BaseTrait;


	/**
	 * 注册普通账号
	 * @param        $type
	 * @param        $account
	 * @param        $password
	 * @param string $register_platform 注册平台
	 * @return bool
	 */
	public function regUser($type, $account, $password, $register_platform = PamAccount::REG_PLATFORM_WEB)
	{
		$roleId = PamRole::where('role_name', PamRole::FE_USER)->value('id');
		if (!$roleId) {
			return $this->setError('用户默认组 ' . PamRole::FE_USER . ' 不存在, 请联系管理员添加后再行注册!');
		}

		$Pam = new ActAccount();
		if (!$Pam->register($type, $account, $password, PamAccount::ACCOUNT_TYPE_USER, $roleId, $register_platform)) {
			return $this->setError($Pam->getError());
		}
		else {
			$this->setPam($Pam->getPam());
			\Event::fire('auth.register', [$register_platform]);
			return true;
		}
	}

	/**
	 * 通过手机号获取账号名 todo 可以优化查询
	 * @param $passport
	 * @return bool|string
	 */
	public function getAccountNameByPassport($passport)
	{
		if (!$passport) {
			return $this->setError('请填写登录账号');
		}

		if (LmUtil::isMobile($passport)) {
			$account_id = AccountFront::where('mobile', $passport)
				->where('v_mobile', 'Y')->value('account_id');
			if ($account_id) {
				$account_name = PamAccount::find($account_id)->account_name;
				return $account_name;
			}
			else {
				return $this->setError('手机号未绑定');
			}
		}

		if (LmUtil::isEmail($passport)) {
			$account_id = AccountFront::where('email', $passport)
				->where('v_email', 'Y')->value('account_id');
			if ($account_id) {
				$account_name = PamAccount::find($account_id)->account_name;
				return $account_name;
			}
			else {
				return $this->setError('邮箱未绑定');
			}
		}

		return $this->setError('登录格式不匹配, 请正确填写');
	}


	/**
	 * 清空支付密码
	 * @return bool
	 */
	public function clearPayword()
	{
		if (!$this->checkUser()) {
			return false;
		}
		$this->user->payword = '';
		$this->user->save();
		return true;
	}

	/**
	 * 修改头像
	 * @param $pic_url
	 * @return bool
	 */
	public function changeHeadPic($pic_url)
	{
		if (!$this->checkUser()) {
			return false;
		}
		$this->user->head_pic = $pic_url;
		$this->user->save();
		return true;
	}

	/**
	 * 修改个人资料
	 * @param $data
	 * @return bool
	 */
	public function changeInfo($data = [])
	{
		if (!$this->checkUser()) {
			return false;
		}
		if (isset($data['nick_name']) && $data['nick_name'] &&
			$this->user->is_nickname_modified != 1 && $data['nick_name'] != $this->user->nickname
		) {
			$this->user->nickname             = $data['nick_name'];
			$this->user->is_nickname_modified = 1;
		}
		if (isset($data['mobile']) && $data['mobile'] && $this->user->v_mobile != 'Y') {
			$this->user->mobile = $data['mobile'];
		}
		if (isset($data['email']) && $data['email']) {
			$this->user->email = $data['email'];
		}
		if (isset($data['qq']) && $data['qq']) {
			$this->user->qq = $data['qq'];
		}
		if (isset($data['area_name']) && $data['area_name']) {
			$this->user->area_name = $data['area_name'];
		}
		if (isset($data['signature']) && $data['signature']) {
			$this->user->signature = $data['signature'];
		}
		$this->user->save();
		return true;
	}

	/**
	 * 设置支付密码
	 * @param string $pay_pwd  支付密码
	 * @param string $pwd_type 支付密码加密方式 [plain|明文;sha1|sha1算法加密]
	 * @return bool
	 */
	public function setPayPwd($pay_pwd, $pwd_type = 'plain')
	{
		if (!$this->checkUser()) {
			return false;
		}
		if ($this->user->payword) {
			return $this->setError('您已经设置了支付密码, 请不要重复设置!');
		}
		AccountFront::changePayword($this->user->account_id, $pay_pwd, $pwd_type);
		return true;
	}

	/**
	 * 修改支付密码
	 * @param string $old_pay_word 原来支付密码
	 * @param string $new_pay_word 新支付密码
	 * @param string $pwd_type     支付密码类型
	 * @return bool
	 */
	public function changePayPwd($old_pay_word, $new_pay_word, $pwd_type = 'plain')
	{
		if (!$this->checkUser()) {
			return false;
		}
		if (!$this->verifyPayPwd($old_pay_word, $pwd_type)) {
			return false;
		}
		AccountFront::changePayword($this->user->account_id, $new_pay_word, $pwd_type);
		return true;
	}

	/**
	 * 验证支付密码  [子账号, 验证主账号,普通账号, 验证自己]
	 * @param string $pay_pwd
	 * @param string $type          验证支付密码的类型   plain|明文;sha1|和注册时间加密过的值
	 * @param int    $verify_times  周期内错误次数
	 * @param int    $verify_period 验证错误时间周期 [60分钟]
	 * @return bool
	 */
	public function verifyPayPwd($pay_pwd, $type = 'plain', $verify_times = 0, $verify_period = 60)
	{
		if (!$this->checkFront()) {
			return false;
		}
		$pay_pwd = trim($pay_pwd);
		if (!$pay_pwd) {
			return $this->setError('输入的支付密码为空');
		}

		// 子账号为父账号, 非子账号为当前账号, 所以这里使用 owner 是没错的
		if (!$this->owner->payword) {
			return $this->setError(['user.pay_pwd_empty']);
		}

		$cacheKey  = cache_name(__CLASS__, 'verify_pay_pwd');
		$cacheData = \Cache::get($cacheKey);
		$key       = 'verify_pay_pwd_' . $this->user->account_id;
		$now       = Carbon::now()->timestamp;
		if ($verify_times) {
			if (array_has($cacheData, $key)) {
				// 检查是否锁定时间内
				$data = array_get($cacheData, $key);
				if ($data['unlock_timestamp']) {
					if ($data['unlock_timestamp'] > $now) {
						// 还在锁定时间内
						$unLockAt = Carbon::createFromTimestamp($data['unlock_timestamp'])->toDateTimeString();
						return $this->setError('账号支付密码锁定, 请在 ' . $unLockAt . ' 后重试!');
					}
					else {
						// 已解锁
						array_forget($cacheData, $key);
						\Cache::forever($cacheKey, $cacheData);
					}
				}
			}
		}

		// 默认是明文传输
		$payPwdCheck = ($type == 'plain' || $type == '')
			? AccountFront::checkPayword($this->owner->account_id, $pay_pwd)
			: AccountFront::checkPayPwdBySha1($this->owner->account_id, $pay_pwd);
		if (!$payPwdCheck) {
			// 有人输错密码的时候清除过期的数据
			if (count($cacheData)) {
				foreach ($cacheData as $key => $value) {
					if ($value['unlock_timestamp'] && $value['unlock_timestamp'] < $now) {
						unset($cacheData[$key]);
					}
				}
				\Cache::forever($cacheKey, $cacheData);
			}


			// 验证支付密码次数
			if ($verify_times) {
				if (!array_has($cacheData, $key)) {
					// 写入缓存
					$cacheData[$key] = [
						'start_timestamp'  => $now,
						'failed_times'     => 1,
						'unlock_timestamp' => 0,
					];
					\Cache::forever($cacheKey, $cacheData);
				}
				else {
					// 检查时间周期
					$data = array_get($cacheData, $key);
					if (($data['start_timestamp'] + $verify_period * 60) < $now) {
						// 过期
						// 重新记录时间范围
						$data            = [
							'start_timestamp'  => $now,
							'failed_times'     => 1,
							'unlock_timestamp' => 0,
						];
						$cacheData[$key] = $data;
						\Cache::forever($cacheKey, $cacheData);
					}
					else {
						// 检查错误次数
						if ($data['failed_times'] >= $verify_times) {
							// 已经超过指定次数
							$data['unlock_timestamp'] = $now + $verify_period * 60;
							$cacheData[$key]          = $data;
							\Cache::forever($cacheKey, $cacheData);
							$unLockAt = Carbon::createFromTimestamp($data['unlock_timestamp'])->toDateTimeString();
							return $this->setError('验证错误次数已经超过' . $verify_times . '次, 请在 ' . $unLockAt . ' 后再试');
						}
						else {
							$data['failed_times'] += 1;
							$cacheData[$key]      = $data;
							\Cache::forever($cacheKey, $cacheData);
							return $this->setError('支付密码错误' . $data['failed_times'] . '次, 超过' . $verify_times . '次将锁定' . $verify_period . '分钟');
						}
					}
				}
			}
			return $this->setError('支付密码错误');
		}
		return true;
	}

	/**
	 * 登录前的检查
	 * @param array $passport      输入的通行证信息
	 *                             [
	 *                             'account_name' => account_name
	 *                             'passport' => zhaody901@qq.com
	 *                             ]
	 * @return bool|mixed|string
	 */
	public function checkBeforeLogin($passport)
	{
		$account_name = strval(array_get($passport, 'account_name'));
		$passport     = strval(array_get($passport, 'passport'));
		if ($account_name && $passport) {
			return $this->setError('请检测一个类型, 不必传两个值过来!');
		}

		if (!($account_name || $passport)) {
			return $this->setError('请输入用户名/手机号/邮箱!');
		}

		if ($passport) {
			if (!($account_name = $this->getAccountNameByPassport($passport))) {
				return false;
			}
		}
		if (!$account_name) {
			return $this->setError('账户名不存在, 请检查填写!');
		}

		if (!PamAccount::accountNameExists($account_name)) {
			return $this->setError('用户名不存在, 请检查输入');
		}
		return $account_name;
	}

	/**
	 * 创建订单的验证程度
	 * @param $data
	 * @return bool
	 */
	public function createTruenameVerify($data)
	{
		if (!$this->checkUser()) {
			return false;
		}
		$chid      = array_get($data, 'chid');
		$truename  = array_get($data, 'truename');
		$validator = \Validator::make([
			'chid'     => $chid,
			'truename' => $truename,
		], [
			'chid'     => 'required|chid',
			'truename' => 'required',
		], [
			'chid.chid'         => '身份证号码格式不正确',
			'chid.required'     => '身份证号码不能为空',
			'truename.required' => '真实姓名不能为空',
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if (AccountFront::where('chid', $chid)->where('truename_status', AccountFront::TRUENAME_STATUS_PASSED)->exists()) {
			return $this->setError('此用户信息已经被认证过, 不允许重复认证!');
		}
		$this->user->update([
			'chid'            => $chid,
			'truename'        => $truename,
			'v_truename'      => AccountFront::Y,
			'truename_status' => AccountFront::TRUENAME_STATUS_PASSED,
		]);
		return true;
	}
}