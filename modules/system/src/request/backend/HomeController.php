<?php namespace System\Request\Backend;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use System\Element\SettingManager;
use System\Models\PamAccount;
use System\Models\SysConfig;


class HomeController extends InitController
{

	/**
	 * 登录
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @internal param LoginRequest $request
	 */
	public function login()
	{
		$auth = \Auth::guard(PamAccount::GUARD_BACKEND);
		if (is_post()) {
			$credentials = [
				'username' => \Input::get('username'),
				'password' => \Input::get('password'),
			];
			$actPam      = app('act.pam');
			if ($actPam->loginCheck($credentials['username'], $credentials['password'], PamAccount::GUARD_BACKEND)) {
				return Resp::web(Resp::SUCCESS, '登录成功', 'location|' . route('backend:home.cp'));
			}
			else {
				return Resp::web(Resp::ERROR, '登录用户名密码不匹配');
			}
		}

		if ($auth->check()) {
			/** @var PamAccount $be */
			$be = $auth->user();
			if ($be->account_type == PamAccount::TYPE_BACKEND) {
				return Resp::web(Resp::SUCCESS, '登录成功', 'location|' . route('backend:home.cp'));
			}
		}
		return view('system::backend.home.login');
	}


	/**
	 * 修改本账户密码
	 * todo
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function password(Request $request)
	{
		if (is_post()) {
			$old_password = $request->input('old_password');
			$password     = trim($request->input('password'));
			$validator    = \Validator::make($request->all(), [
				'password'     => 'required|confirmed',
				'old_password' => 'required',
			]);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->errors());
			}


			/** @var PamAccount $pam */
			$pam    = $this->getBeGuard()->user();
			$actPam = app('act.pam');
			if (!$actPam->checkPassword($pam, $old_password)) {
				return Resp::web(Resp::ERROR, '原密码错误!');
			}

			$actPam->setPassword($pam, $password);
			\Auth::logout();
			return Resp::web(Resp::SUCCESS, '密码修改成功, 请重新登录', 'location|' . route('backend:home.login'));
		}
		return view('system::backend.home.password');
	}

	/**
	 * 登出
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function logout()
	{
		\Auth::guard(PamAccount::GUARD_BACKEND)->logout();
		return Resp::web(Resp::SUCCESS, '退出登录', 'location|' . route('backend:home.login'));
	}

	/**
	 * 控制面板
	 * @return \Illuminate\View\View
	 */
	public function cp()
	{
		return view('system::backend.home.cp');
	}

	/**
	 * Setting
	 * @param Request $request
	 * @param string  $path
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function setting(Request $request, $path = 'setting-system')
	{
		$Setting = new SettingManager($path);
		if (is_post()) {
			if (!$Setting->save($request)) {
				return Resp::web(Resp::ERROR, $Setting->getError(), 'forget|1');
			}
			return Resp::web(Resp::SUCCESS, '更新配置成功', 'forget|!');
		}
		return $Setting->render();
	}

	/**
	 * 更新缓存
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function cache()
	{
		SysConfig::cacheClear();
		SysAcl::reCache();
		return Resp::web(Resp::SUCCESS, '更新缓存成功', 'location|message');
	}

	public function info()
	{
		phpinfo();
	}

}