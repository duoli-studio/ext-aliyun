<?php namespace Order\Http\Api\Controllers;

use Dingo\Api\Contract\Http\Request;
use Poppy\Framework\Application\ApiController;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends ApiController
{
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
}