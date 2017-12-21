<?php namespace System\Request\Middleware;


use Closure;
use System\Models\PamAccount;
use Poppy\Framework\Classes\Resp;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class Auth
{

	/**
	 * The Guard implementation.
	 * @var Guard
	 */
	protected $guard;

	/**
	 * Create a new filter instance.
	 * @param  AuthFactory $auth
	 */
	public function __construct(AuthFactory $auth)
	{
		$this->guard = $auth->guard(PamAccount::GUARD_BE);
	}

	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		if ($this->guard->guest()) {
			return Resp::web(Resp::ERROR, '登陆已过期!', 'location|' . route('dsk_lemon_home.login'));
		}


		$user = $this->guard->user();
		if ($user->account_type != PamAccount::ACCOUNT_TYPE_DESKTOP) {
			return Resp::web('只有管理员才能访问后台， 其他用户类型不可以!', 'location|' . route('dsk_lemon_home.login'));
		}

		$routeName = \Route::currentRouteName();
		/*
		hide rbac
		$permission = SysAcl::getPermissionCache(SysAcl::TYPE_DESKTOP);
		if ($routeName && !isset($permission[$routeName])) {
			return $next($request);
		}

		if ($routeName && !\Rbac::capable($routeName)) {
			// 如果是后台权限不足, 则检查 rbac 的初始或或者 ACL 对项目的定义
			return AppWeb::resp(AppWeb::ERROR, '后台权限不足, 您无权访问本模块!');
		}*/
		return $next($request);
	}

}



