<?php namespace System\Pam\Action;

/**
 * 基本账户操作
 */
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use Util\Models\PamCaptcha;
use Util\Util\Action\Util;


class BindChange
{

	use SystemTrait;

	/** @var PamAccount */
	protected $accountId;

	/** @var int Role id */
	protected $mobile;

	/**
	 * @var string
	 */
	protected $accountTable;

	public function __construct()
	{
		$this->accountTable = (new PamAccount())->getTable();
	}

	public function oldSendCaptcha()
	{
		if (!$this->checkPermission()) {
			return false;
		}
		// init
		if ($this->pam->id && !$this->initAccount($this->pam->id)) {
			return false;
		}
		$actUtil = app('act.util');
		$actUtil->sendCaptcha($this->mobile, $type = PamCaptcha::CON_FIND_PASSWORD);
		//忘记手机号，手机号失效的情况

		return true;
	}

	//验证
	public function oldValidator($captcha)
	{
		$actUtil = app('act.util');
		if (!$actUtil->validCaptcha($this->mobile, $captcha)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		$actUtil->deleteCaptcha($this->mobile, $captcha);
		return true;
	}

	public function newSendCaptcha($newMobile)
	{
		//验证新手机号是否已经注册
		if (PamAccount::where('mobile', $newMobile)->count()) {
			return $this->setError('该手机号已经注册过');
		} else {
			//发送验证码
			$actUtil = app('act.util');
			$actUtil->sendCaptcha($newMobile, $type = PamCaptcha::CON_REGISTER);
		}
		return true;
	}

	//新的手机号验证
	public function newValidator($newMobile, $newCaptcha)
	{
		$actUtil = app('act.util');
		if (!$actUtil->validCaptcha($newMobile, $newCaptcha)) {
			return $this->setError($actUtil->getError()->getMessage());
		}
		PamAccount::where('id', $this->pam->id)->update([
			'mobile' => $newMobile,
		]);
		$actUtil->deleteCaptcha($newMobile, $newCaptcha);
		return true;
	}


	public function initAccount($id)
	{
		try {
			$this->accountId = PamAccount::findOrFail($id);
			$this->mobile    = $this->accountId->mobile;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}
	}
}