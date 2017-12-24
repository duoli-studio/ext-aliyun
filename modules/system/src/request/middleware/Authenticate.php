<?php namespace System\Request\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use System\Classes\Traits\SystemTrait;

/**
 * Class Authenticate.
 */
class Authenticate extends IlluminateAuthenticate
{
	use SystemTrait;

	/**
	 * @param array $guards
	 * @return null|void
	 * @throws AuthenticationException
	 */
	protected function authenticate(array $guards)
	{
		if (empty($guards)) {
			return $this->getAuth()->authenticate();
		}
		foreach ($guards as $guard) {
			// 开启调试模式
			if ($this->getSetting()->get('system::setting/site.debug_testing', false) && $guard == 'api') {
				return null;
			}
			if ($this->getAuth()->guard($guard)->check()) {
				return $this->getAuth()->shouldUse($guard);
			}
		}

		throw new AuthenticationException('Unauthenticated.', $guards);
	}
}
