<?php namespace Develop\Request\Middleware;

use Closure;
use Poppy\Framework\Classes\Resp;
use User\Classes\AclHelper;

class Develop
{


	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (\Auth::guest()) {
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			}
			else {
				return Resp::web(Resp::ERROR, '无权限访问', 'location|' . route('dev_home.login'));
			}
		}

		if (\Auth::user()->account_type != PamAccount::ACCOUNT_TYPE_DEVELOP) {
			return Resp::web(Resp::ERROR, '用户账户类型不正确, 请登陆后访问', 'location|' . route('dev_home.login'));
		}
		$routeName  = \Route::currentRouteName();
		$permission = AclHelper::getPermissionCache('develop');
		if ($routeName && !isset($permission[$routeName])) {
			return $next($request);
		}

		if ($routeName && !\Rbac::capable($routeName)) {
			return Resp::web(Resp::ERROR, 'rbac 权限不足, 您无权访问本模块!', 'location|back');
		}
		return $next($request);
	}


}
