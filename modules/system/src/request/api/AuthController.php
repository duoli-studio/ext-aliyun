<?php namespace System\Request\Api;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\Resources\PamResource;

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
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function token(): JsonResponse
	{
		$this->validate($this->getRequest(), [
			'username' => Rule::required(),
			'password' => Rule::required(),
		], [
			'username.required' => '用户名必须填写',
			'password.required' => '用户密码必须填写',
		]);

		if ($this->hasTooManyLoginAttempts($this->getRequest())) {
			$seconds = $this->limiter()->availableIn($this->throttleKey($this->getRequest()));
			$message = $this->getTranslator()->get('auth.throttle', ['seconds' => $seconds]);
			return $this->getResponse()->json([
				'message' => $message,
			], 403, [], JSON_UNESCAPED_UNICODE);
		}
		if (!$this->guard()->attempt($this->getRequest()->only([
			'username',
			'password',
		], $this->getRequest()->has('remember', true)))) {
			return $this->getResponse()->json([
				'message' => '认证失败！',
			], 403, [], JSON_UNESCAPED_UNICODE);
		}
		/* Do not use session
		 -------------------------------------------- */
		// $this->getRequest()->session()->regenerate();
		$this->clearLoginAttempts($this->getRequest());
		$user = $this->guard()->user();
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
	 * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|mixed
	 */
	public function guard()
	{
		return $this->getAuth()->guard(PamAccount::GUARD_JWT_BACKEND);
	}

	/**
	 * @return string
	 */
	public function username()
	{
		return 'username';
	}
}
