<?php namespace System\Request\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as IlluminateAuthenticate;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;

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
			if ($this->getSetting()->get('system::site.debug_testing', false) && $guard == 'api') {
				return null;
			}
			if ($this->getAuth()->guard($guard)->check()) {
				return $this->getAuth()->shouldUse($guard);
			}
		}
		throw new AuthenticationException('Unauthenticated.', $guards);
	}


	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @param array                     $guards
	 * @return mixed
	 */
	public function handle($request, Closure $next, ...$guards)
	{
		try {
			$this->authenticate($guards);
		} catch (\Exception $e) {
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			}
			elseif ($this->getPoppy()->exists('develop') && in_array('develop', $guards)) {
				return Resp::web(Resp::ERROR, '无权限访问', 'location|' . route('system:develop.login'));
			}
		}

		/*
		$routeName  = \Route::currentRouteName();
		$permission = AclHelper::getPermissionCache('develop');
		if ($routeName && !isset($permission[$routeName])) {
			return $next($request);
		}

		if ($routeName && !\Rbac::capable($routeName)) {
			return Resp::web(Resp::ERROR, 'rbac 权限不足, 您无权访问本模块!', 'location|back');
		}
		*/
		return $next($request);
	}
}
