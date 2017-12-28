<?php namespace System\Request\Backend;

use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use System\Models\SysConfig;
use System\Models\PamAccount;
use System\Models\PamRole;


class BeHomeController extends Controller
{
	/**
	 * 登录
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @internal param LoginRequest $request
	 */
	public function login()
	{
		$auth = \Auth::guard(PamAccount::ACCOUNT_TYPE_BACKEND);
		if (is_post()) {
			$credentials = [
				'account_type' => PamAccount::ACCOUNT_TYPE_BACKEND,
				'account_name' => \Input::get('adm_name'),
				'password'     => \Input::get('adm_pwd'),
			];

			$validator = \Validator::make($credentials, [
				'account_name' => 'required',
				'password'     => 'required',
			]);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->messages());
			}


			if ($auth->once($credentials)) {
				/** @var PamAccount $account */
				$account = $auth->user();
				if (!$account->hasRole(PamRole::BE_ROOT)) {
					// check is_enable
					if ($account->is_enable == SysConfig::NO) {
						return Resp::web(Resp::ERROR, '用户被禁用');
					}
				}

				$auth->login($account, true);

				return Resp::web(Resp::SUCCESS, '登录成功', 'location|' . route('be:home.welcome'));
			}
			else {
				\Event::fire('auth.failed', [$credentials]);
				return Resp::web(Resp::ERROR, '登录用户名密码不匹配');
			}
		}

		if ($auth->check()) {
			/** @var PamAccount $be */
			$be = $auth->user();
			if ($be->account_type == PamAccount::ACCOUNT_TYPE_BACKEND) {
				return Resp::web(Resp::SUCCESS, '登录成功', 'location|' . route('be:home.welcome'));
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
		if (\Input::method() == 'POST') {
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
		\Auth::guard($this->accountType)->logout();
		return Resp::web(Resp::SUCCESS, '退出登录', 'location|' . route('be:home.login'));
	}

	/**
	 * 控制面板
	 * @return \Illuminate\View\View
	 */
	public function getCp()
	{
		$menus = SysAcl::menu(PamAccount::ACCOUNT_TYPE_BACKEND, $this->pam, true);
		return view('backend.lemon_home.cp', [
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