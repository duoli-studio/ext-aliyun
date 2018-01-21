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
				return response()->json([
					"message" => 'Unauthorized',
				], 401);
			}
			// develop
			elseif (in_array(PamAccount::GUARD_DEVELOP, $guards)) {
				return Resp::web(Resp::ERROR, '无权限访问', 'location|' . route('system:develop.pam.login'));
			}
			// backend
			elseif (in_array(PamAccount::GUARD_BACKEND, $guards)) {
				return Resp::web(Resp::ERROR, '无权限访问', 'location|' . route('backend:home.login'));
			}
			elseif (in_array(PamAccount::GUARD_WEB, $guards)) {
				$appends = [];
				if (config('poppy.guard_location.web')) {
					$appends = [
						'location' => route(config('poppy.guard_location.web')),
					];
				}
				return Resp::web(Resp::ERROR, '无权限访问', $appends);
			}
			else {
				return response('Unauthorized.', 401);
			}
		}
		return $next($request);
	}
}
