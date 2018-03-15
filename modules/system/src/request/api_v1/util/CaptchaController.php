<?php namespace System\Request\ApiV1\Util;

use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use System\Action\ImageCaptcha;
use System\Action\Pam;
use System\Action\Verification;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\PamCaptcha;

class CaptchaController extends ApiController
{
	use SystemTrait;

	/**
	 * @api                 {get} api_v1/util/captcha/image [O]图像验证码
	 * @apiDescription      通过url地址过去验证图片, 用于发送验证码的流量限制
	 * @apiVersion          1.0.0
	 * @apiName             UtilCaptchaDisplay
	 * @apiGroup            Util
	 * @apiParam   {String} mobile   手机号
	 */
	public function image()
	{
		$input     = [
			'mobile' => \Input::get('mobile'),
		];
		$validator = \Validator::make($input, [
			'mobile' => 'required|mobile',
		], [], [
			'mobile' => '手机号',
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->messages(), 'json|1');
		}

		return (new ImageCaptcha())->generate($input['mobile']);
	}

	/**
	 * @api                 {post} api_v1/util/captcha/send [O]验证码
	 * @apiDescription      发送短信验证码
	 * @apiVersion          1.0.0
	 * @apiName             UtilSmsSend
	 * @apiGroup            Util
	 * @apiParam   {String} [passport]    通行证
	 * @apiParam   {String} [image_code]  图片验证码(开发模式可不填写)
	 * @apiParam   {String} type          操作类型
	 *             [login|登录/注册;password|找回密码;user|给自己;rebind|绑定新手机;]
	 */
	public function send()
	{
		$passport   = input('passport', '');
		$image_code = input('image_code', '');
		$type       = input('type', '');

		$passportType = (new Pam())->passportType($passport);

		switch ($type) {
			case PamCaptcha::CON_LOGIN:
				// 登录, 需要手机号, 需要图形验证码
				if (!$passport) {
					return Resp::web(Resp::ERROR, '请填入需要发送的通行证手机号');
				}

				// 开发环境不检测图形验证码
				if (is_production()) {
					if (!$image_code) {
						return Resp::web(Resp::ERROR, '请输入图形验证码');
					}
					$ImageCaptcha = (new ImageCaptcha());
					if (!$ImageCaptcha->check($passport, $image_code)) {
						return Resp::web(Resp::ERROR, $ImageCaptcha->getError());
					}
				}
				break;
			case PamCaptcha::CON_PASSWORD:

				// 需要验证手机号不为空
				if (!$passport) {
					return Resp::web(Resp::ERROR, '请填入需要发送的通行证手机号');
				}

				// 系统中需要存在这个账号
				if (!PamAccount::where($passportType, $passport)->exists()) {
					return Resp::web(Resp::ERROR, trans('system::action.captcha.account_miss'));
				}
				break;
			case PamCaptcha::CON_USER:
				// 需要授权
				/** @var PamAccount $jwtUser */
				$jwtUser = $this->getJwtWebGuard()->user();
				if (!$jwtUser) {
					return Resp::web(Resp::ERROR, '你需要登录之后才能发送');
				}
				$passport = $jwtUser->mobile;
				break;
			case PamCaptcha::CON_REBIND:
				// 需要授权
				$jwtUser = $this->getJwtWebGuard()->user();
				if (!$jwtUser) {
					return Resp::web(Resp::ERROR, '你需要登录之后才能发送');
				}

				// 需要验证手机号不为空
				if (!$passport) {
					return Resp::web(Resp::ERROR, '请填入需要发送的通行证手机号');
				}

				// 新绑定手机号不能存在
				if (PamAccount::where($passportType, $passport)->exists()) {
					return Resp::web(Resp::ERROR, trans('system::action.captcha.account_exists'));
				}
				break;
		}

		/** @var Verification $Verification */
		$Verification = (new Verification());
		if (!$Verification->send($passport, $type)) {
			return Resp::web(Resp::ERROR, $Verification->getError());
		}
		 
			$captcha = $Verification->getCaptcha();
			$tip     = trans('system::util.captcha.ctl.send_success');
			$tip .= !is_production() ? ', 验证码是: ' . $captcha->captcha : '';

			return Resp::web(Resp::SUCCESS, $tip);
	}

	/**
	 * @api                 {post} api_v1/util/captcha/verify_code [O]验证串
	 * @apiDescription      获取验证串, 以方便下一步操作
	 * @apiVersion          1.0.0
	 * @apiName             UtilCaptchaVerifyCode
	 * @apiGroup            Util
	 * @apiParam   {String} passport      通行证
	 * @apiParam   {String} captcha       验证码
	 * @apiSuccessExample   data
	 * {
	 *     "verify_code": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*******"
	 * }
	 */
	public function verifyCode()
	{
		$passport = input('passport', '');
		$captcha  = input('captcha', '');

		$Verification = (new Verification());
		if (!$Verification->check($passport, $captcha)) {
			return Resp::web(Resp::ERROR, $Verification->getError());
		}
		 
			$Verification->delete($passport);

			return Resp::web(Resp::SUCCESS, '操作成功', [
				'verify_code' => $Verification->genOnceVerifyCode(10, $passport),
			]);
	}
}
