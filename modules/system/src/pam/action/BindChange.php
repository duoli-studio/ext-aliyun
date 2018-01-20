<?php namespace System\Pam\Action;

/**
 * 手机换绑操作
 */
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\User\Action\User;
use Util\Models\PamCaptcha;


class BindChange
{

	use SystemTrait;

	/**
	 * 原手机号发送验证码
	 * @param $mobile
	 * @param $captcha
	 * @return bool
	 */
	public function oldSendCaptcha($mobile, $captcha)
	{
		if (!$this->checkPermission()) {
			return false;
		}
		if (!(new User())->existsPassword($this->pam)) {
			return false;
		}

		$actUtil = app('act.util');

		if ($mobile != $this->pam->mobile) {
			return $this->setError('该手机号不是本账号绑定手机');
		}
		$actUtil->sendCaptcha($mobile, $type = PamCaptcha::CON_FIND_PASSWORD);
		//考虑忘记手机号，手机号失效的情况

		return true;
	}

	/**
	 * 原手机验证
	 * @param $captcha
	 * @return bool|string
	 * @throws \Exception
	 */
	public function oldValidate($captcha)
	{
		$actUtil = app('act.util');
		if (!$actUtil->validCaptcha($this->pam->mobile, $captcha)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		$actUtil->deleteCaptcha($this->pam->mobile, $captcha);
		//生成串码 包含有效期
		try {
			$verify_code = $actUtil->genOnceVerifyCode(10, $this->pam->mobile);
			return $verify_code;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
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
		$actUtil = app('act.util');
		if (!$actUtil->verifyOnceCode($verify_code)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		//验证新手机号是否已经注册
		if (PamAccount::where('mobile', $newMobile)->exists()) {
			return $this->setError('该手机号已经注册过');
		}

		//发送验证码
		$actUtil->sendCaptcha($newMobile, $type = PamCaptcha::CON_REGISTER);
		return true;
	}

	/**
	 * 新的手机号验证
	 * @param $newMobile
	 * @param $newCaptcha
	 * @return bool
	 * @throws \Exception
	 */
	public function newValidate($newMobile, $newCaptcha)
	{
		if (!$this->checkPermission()) {
			return false;
		}
		$actUtil = app('act.util');

		if (!$actUtil->validCaptcha($newMobile, $newCaptcha)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		$actUtil->deleteCaptcha($newMobile, $newCaptcha);

		try {
			$this->pam->update([
				'mobile' => $newMobile,
			]);
			return true;
		} catch (\Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

}