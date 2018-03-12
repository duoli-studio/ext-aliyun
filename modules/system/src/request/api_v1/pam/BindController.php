<?php namespace System\Request\ApiV1\Pam;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;
use System\Action\OAuth;
use System\Action\Pam;


class BindController extends ApiController
{
	use SystemTrait, ThrottlesLogins;

	/**
	 * @api                  {post} api_v1/pam/bind/cancel [O]解绑第三方
	 * @apiVersion           1.0.0
	 * @apiName              PamBindCancel
	 * @apiGroup             Pam
	 * @apiParam {String}    type         解绑第三方 [qq|QQ;wx|微信]
	 */
	public function cancel(): JsonResponse
	{
		$type = input('type', 'qq');

		$OAuth = new OAuth();
		if (!$OAuth->setPam($this->getJwtWebGuard()->user())->unbind($type)) {
			return Resp::web(Resp::ERROR, $OAuth->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '绑定成功');
		}
	}

	/**
	 * @api                  {post} api_v1/pam/bind/new_passport [O]更换通行证
	 * @apiVersion           1.0.0
	 * @apiName              PamBindNewPassport
	 * @apiGroup             Pam
	 * @apiParam {String}    passport        通行证
	 * @apiParam {String}    captcha         验证码
	 * @apiParam {String}    verify_code     验证串码(旧验证有效期)
	 */
	public function newPassport()
	{
		$passport    = input('passport', '');
		$captcha     = input('captcha', '');
		$verify_code = input('verify_code', '');

		$Pam = (new Pam())->setPam($this->getJwtWebGuard()->user());
		if (!$Pam->newPassport($verify_code, $passport, $captcha)) {
			return Resp::web(Resp::ERROR, $Pam->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '更换成功');
		}
	}
}
