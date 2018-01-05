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


	//账号换绑
	//
	public function establish($accountId, $captcha)
	{
		// init
		if ($accountId && !$this->initAccount($accountId)) {
			return false;
		}

		//给该手机号发送验证码
		$util = new Util();
		$util->sendCaptcha($this->mobile);
		//如果手机号失效，调用忘记手机号的方法验证

		//验证手机验证码
		$getCaptchas = PamCaptcha::where('passport', $this->mobile)->get(['captcha'])->toArray();
		$getCaptcha = $getCaptchas[0]['captcha'];
		if ($captcha == $getCaptcha) {
			return true;
		} else {
			return $this->setError('操作失败');
		}

	}

	//新的手机号验证
	public function validator($accountId, $newMobile, $newCaptcha)
	{
		//验证新手机号是否已经注册
		$count = PamAccount::where('mobile', $newMobile)->count();
		if ($count){
			return $this->setError('该手机号已经注册过');
		}else{
			//发送验证码
			(new Util())->sendCaptcha($newMobile);

			$getCaptchas = PamCaptcha::where('passport', $newMobile)->get('captcha')->toArray();
			$getCaptcha = $getCaptchas[0]['captcha'];
			if($newCaptcha == $getCaptcha) {

				// $this->accountTable->mobile = $newMobile;
				// $this->accountTable->save();
				PamAccount::where('id',$accountId)->update(['mobile' => $newMobile]);

				return true;
			}else{
				return $this->setError('操作失败');
			}
		}

	}


	public function initAccount($accountId)
	{
		try {
			$this->accountId = PamAccount::findOrFail($accountId);
			$this->mobile    = $this->accountId->mobile;
			return true;
		} catch (\Exception $e) {
			return $this->setError('条目不存在, 不得操作');
		}
	}
}