<?php namespace System\Request\Backend;

use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\SysConfig;


class HomeController extends Controller
{
	use SystemTrait;

	public function __construct()
	{
		parent::__construct();
		\View::share([
			'_pam' => \Auth::guard(PamAccount::GUARD_BACKEND)->user(),
		]);
	}

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
			$validator = \Validator::make($request->all(), [
				'password'     => 'required|confirmed',
				'old_password' => 'required',
			]);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->errors());
			}

			$old_password = $request->input('old_password');
			if ($old_password) {
				if (!app('act.pam')->checkPassword($this->pam, $old_password)) {
					return Resp::web(Resp::ERROR, '原密码错误!');
				}
			}


			$password = $request->input('password');
			PamAccount::changePwd(\Auth::id(), $password);
			\Auth::logout();
			return Resp::web(Resp::SUCCESS, trans('desktop.edit_password_ok_and_relogin'), 'location|' . route('dsk.lemon_home.login'));
		}
		return view('backend.lemon_home.password');
	}

	/**
	 * 登出
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function logout()
	{
		\Auth::guard(PamAccount::GUARD_BACKEND)->logout();
		return Resp::web(Resp::SUCCESS, '退出登录', 'location|' . route('be:home.login'));
	}

	/**
	 * 控制面板
	 * @return \Illuminate\View\View
	 */
	public function cp()
	{
		$menus = $this->getModule()->backendMenus()->toArray();
		return view('system::backend.home.cp', [
			'menus' => $menus,
		]);

	}


	public function test()
	{
		// test
	}

	public function setting(Request $request, $file = 'site')
	{
		$Setting = new SettingManager('backend::' . $file);
		if ($request->method() == 'POST') {
			$group = \Input::get('_group');
			if (!$Setting->save($request->all(), $group)) {
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