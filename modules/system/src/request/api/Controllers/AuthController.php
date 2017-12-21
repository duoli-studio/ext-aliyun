<?php namespace System\Request\Api\Controllers;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends ApiController
{
	use ThrottlesLogins;

	public function token()
	{
		$this->validate($this->getRequest(), [
			'name'     => Rule::required(),
			'password' => Rule::required(),
		], [
			'name.required'     => '用户名必须填写',
			'password.required' => '用户密码必须填写',
		]);
		if ($this->hasTooManyLoginAttempts($this->request)) {
			$seconds = $this->limiter()->availableIn($this->throttleKey($this->request));
			$message = $this->translator->get('auth.throttle', ['seconds' => $seconds]);

			return $this->response->json([
				'message' => $message,
			], 403);
		}
		if (!$this->auth->guard()->attempt($this->request->only([
			'name',
			'password',
		], $this->request->has('remember', true)))) {
			return $this->response->json([
				'message' => '认证失败！',
			], 403);
		}
		$this->request->session()->regenerate();
		$this->clearLoginAttempts($this->request);
		$user = $this->auth->guard()->user();
		if (!$token = $this->jwt->fromUser($user)) {
			return $this->response->json(['error' => 'invalid_credentials'], 401);
		}

		return $this->response->json([
			'data'    => $token,
			'message' => '获取 Token 成功！',
		]);
	}

	public function authenticate(Request $request)
	{
		// grab credentials from the request
		$credentials = $request->only('account_name', 'password');
		try {
			// attempt to verify the credentials and create a token for the user
			if (!$token = \JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch (JWTException $e) {
			// something went wrong whilst attempting to encode the token
			return response()->json(['error' => 'could_not_create_token'], 500);
		}
		// all good so return the token
		return response()->json(compact('token'));
	}

	public function register()
	{

	}

	public function getAuthenticatedUser()
	{
		try {
			if (!$user = \JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}
		} catch (TokenExpiredException $e) {
			return response()->json(['token_expired'], $e->getCode());
		} catch (TokenInvalidException $e) {
			return response()->json(['token_invalid'], $e->getCode());
		} catch (JWTException $e) {
			return response()->json(['token_absent'], $e->getCode());
		}
		// the token is valid and we have found the user via the sub claim
		return response()->json(compact('user'));
	}

	/**
	 * 这里是 ThrottlesLogins 设置的登录用户名
	 * @return string
	 */
	public function username()
	{
		return 'account_name';
	}
}