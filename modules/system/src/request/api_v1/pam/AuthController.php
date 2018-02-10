<?php namespace System\Request\ApiV1\Pam;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use System\Action\Verification;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\Resources\PamResource as PamResource;
use System\Action\Pam;


class AuthController extends ApiController
{
	use SystemTrait, ThrottlesLogins;

	/**
	 * @api                  {post} api_v1/pam/auth/access [O]检测 Token 可用
	 * @apiVersion           1.0.0
	 * @apiName              PamAuthAccess
	 * @apiGroup             Pam
	 * @apiParam {String}    token            Token
	 * @apiSuccess {Integer}    id              ID
	 * @apiSuccess {String}     username        用户名
	 * @apiSuccess {String}     mobile          手机号
	 * @apiSuccess {String}     email           邮箱
	 * @apiSuccess {String}     type            用户类型
	 * @apiSuccess {Int}        is_enable       启用/禁用
	 * @apiSuccessExample    data
	 * {
	 *     "id": 3,
	 *     "username": "fadan001",
	 *     "mobile": "18766482988",
	 *     "email": "",
	 *     "password": "34e6ffe64017f5ff509814f7106d3c0c",
	 *     "type": "user",
	 *     "is_enable": 1,
	 *     "disable_reason": "",
	 *     "created_at": "2018-01-02 16:08:01",
	 *     "updated_at": "2018-01-31 10:28:13"
	 * }
	 */
	public function access(): JsonResponse
	{
		if (!$user = $this->getJwt()->parseToken()->authenticate()) {
			return $this->getResponse()->json([
				'message' => '登录失效，请重新登录！',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		return Resp::web(Resp::SUCCESS, '有效登录',
			(new PamResource($user))->toArray($this->getRequest())
		);
	}

	/**
	 * @api                    {post} api_v1/pam/auth/token/:guard [O]获取Token
	 * @apiVersion             1.0.0
	 * @apiName                PamAuthToken
	 * @apiGroup               Pam
	 * @apiParam {String}      :guard          web|Web;backend|后台;
	 * @apiParam {String}      passport        通行证
	 * @apiParam {String}      password        密码
	 * @apiSuccess {String}    token           认证成功的Token
	 * @apiSuccessExample      data
	 * {
	 *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*******"
	 * }
	 */
	public function token($guard): JsonResponse
	{
		$this->validate($this->getRequest(), [
			'passport' => Rule::required(),
			'password' => Rule::required(),
		], [
			'passport.required' => '用户名必须填写',
			'password.required' => '用户密码必须填写',
		]);

		if ($this->hasTooManyLoginAttempts($this->getRequest())) {
			$seconds = $this->limiter()->availableIn($this->throttleKey($this->getRequest()));
			$message = $this->getTranslator()->get('auth.throttle', ['seconds' => $seconds]);
			return $this->getResponse()->json([
				'message' => $message,
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		$credentials = (new Pam)->passportData($this->getRequest());

		if (!$this->guard($guard)->attempt($credentials)) {
			return $this->getResponse()->json([
				'message' => '认证失败',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		$this->clearLoginAttempts($this->getRequest());
		/** @var PamAccount $user */
		$user = $this->guard($guard)->user();
		if (!$token = $this->getJwt()->fromUser($user)) {
			return $this->getResponse()->json([
				'message' => '凭证有误',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}
		return $this->getResponse()->json([
			'token'   => $token,
			'message' => '认证通过',
			'status'  => Resp::SUCCESS,
		], 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @api                    {post} api_v1/pam/auth/reset_password [O]重设密码
	 * @apiVersion             1.0.0
	 * @apiName                PamResetPassword
	 * @apiGroup               Pam
	 * @apiParam {String}      verify_code     验证串
	 * @apiParam {String}      password        密码
	 */
	public function resetPassword()
	{
		$verify_code = input('verify_code', '');
		$password    = input('password', '');

		$Verification = (new Verification);
		if ($Verification->verifyOnceCode($verify_code)) {
			$passport = $Verification->getHiddenStr();
			$Pam      = new Pam();
			$pam      = PamAccount::passport($passport);
			if ($Pam->setPassword($pam, $password)) {
				return Resp::web(Resp::SUCCESS, '操作成功');
			}
			else {
				return Resp::web(Resp::ERROR, $Pam->getError());
			}
		}
		else {
			return Resp::web(Resp::ERROR, $Verification->getError());
		}
	}

	/**
	 * @api                    {post} api_v1/pam/auth/login [O]登录-无资料
	 * @apiVersion             1.0.0
	 * @apiName                PamAuthLogin
	 * @apiGroup               Pam
	 * @apiParam {String}      passport      通行证
	 * @apiParam {String}      [captcha]     验证码
	 * @apiParam {String}      [password]    密码
	 * @apiSuccess {String}    token         Token
	 * @apiSuccessExample      data
	 * {
	 *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.*****.****-****",
	 * }
	 */
	public function login()
	{

		$passport = input('passport', '');
		$captcha  = input('captcha', '');
		$password = input('password', '');

		if (!$captcha && !$password) {
			return Resp::web(Resp::ERROR, trans('user::user.graphql.login_key_need'));
		}

		/** @var Pam $Pam */
		$Pam = (new Pam());
		if ($captcha) {
			if (!$Pam->captchaLogin($passport, $captcha)) {
				return Resp::web(Resp::ERROR, $Pam->getError());
			}
		}
		else {
			if (!$Pam->loginCheck($passport, $password, PamAccount::GUARD_JWT_WEB)) {
				return Resp::web(Resp::ERROR, $Pam->getError());
			}
		}
		$pam = $Pam->getPam();

		if (!$token = $this->getJwt()->fromUser($pam)) {
			return Resp::web(Resp::ERROR, trans('user::login.graphql.get_token_fail'));
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功', [
				'token' => $token,
			]);
		}
	}

	protected function guard($guard)
	{
		if ($guard == 'web') {
			$guard = PamAccount::GUARD_JWT_WEB;
		}
		else {
			$guard = PamAccount::GUARD_JWT_BACKEND;
		}
		return $this->getAuth()->guard($guard);
	}

	protected function username()
	{
		return 'passport';
	}
}
