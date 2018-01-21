<?php namespace System\Request\System\Api;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\Resources\PamResource;
use System\Pam\Action\Pam;

/**
 * Class AdministrationController.
 */
class AuthController extends Controller
{
	use SystemTrait, ThrottlesLogins;

	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Tymon\JWTAuth\Exceptions\JWTException
	 */
	public function access(): JsonResponse
	{
		if (!$user = $this->getJwt()->parseToken()->authenticate()) {
			return $this->getResponse()->json([
				'message' => '登录失效，请重新登录！',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		return $this->getResponse()->json([
			'data'    => new PamResource($user),
			'message' => '有效登录',
		], 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param string $guard
	 * @return \Illuminate\Http\JsonResponse
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
			], 403, [], JSON_UNESCAPED_UNICODE);
		}

		$credentials = (new Pam)->passportData($this->getRequest());

		if (!$this->guard($guard)->attempt($credentials)) {
			return $this->getResponse()->json([
				'message' => '认证失败！',
			], 403, [], JSON_UNESCAPED_UNICODE);
		}

		$this->clearLoginAttempts($this->getRequest());
		/** @var PamAccount $user */
		$user = $this->guard($guard)->user();
		if (!$token = $this->getJwt()->fromUser($user)) {
			return $this->getResponse()->json([
				'error' => 'invalid_credentials',
			], 401, [], JSON_UNESCAPED_UNICODE);
		}

		return $this->getResponse()->json([
			'data'    => $token,
			'message' => '获取 Token 成功！',
		], 200, [], JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param $guard
	 * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|mixed
	 */
	public function guard($guard)
	{
		if ($guard == 'web') {
			$guard = PamAccount::GUARD_JWT_WEB;
		}
		else {
			$guard = PamAccount::GUARD_JWT_BACKEND;
		}
		return $this->getAuth()->guard($guard);
	}

	/**
	 * @return string
	 */
	public function username()
	{
		return 'username';
	}
}
