<?php namespace System\Request\Middleware;

use Closure;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\SysConfig;
use Tymon\JWTAuth\JWTGuard;

class DisabledPam
{
	use SystemTrait;

	/**
	 * Handle an incoming request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		/** @var JWTGuard $guard */
		$guard = $this->getAuth()->guard();
		if ($guard->check()) {
			/** @var PamAccount $user */
			$user = $guard->user();
			if ($user->is_enable == SysConfig::NO) {
				return Resp::web(Resp::ERROR, '用户被禁用, 原因 : ' . $user->disable_reason . ', 解禁时间 : ' . $user->disable_end_at);
			}
		}

		return $next($request);
	}
}