<?php namespace System\Request\Backend\Controllers;


use Poppy\Framework\Application\Traits\PoppyTrait;
use Poppy\Framework\Application\Traits\ViewTrait;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as AuthFactory;


class LemonHomeController extends InitController
{
	use ViewTrait, PoppyTrait;

	public function layout()
	{
		$this->share('translations', json_encode($this->translator->fetch('zh')));
		return view('system::layout');
	}

	/**
	 * 登录
	 * @param Request     $request
	 * @param AuthFactory $auth
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function login(Request $request, AuthFactory $auth)
	{
		$guard = $auth->guard(PamAccount::GUARD_BE);
		if (is_post()) {
			$inputs      = $request->only('adm_name', 'adm_pwd');
			$credentials = [
				'account_type' => PamAccount::ACCOUNT_TYPE_DESKTOP,
				'account_name' => $inputs['adm_name'],
				'password'     => $inputs['adm_pwd'],
			];

			if ($guard->once($credentials)) {
				if (!\Rbac::hasRole(PamAccount::GUARD_BE, 'root')) {

					/*
					if (site('dsk_enable_login_ip') == 'Y') {
						// check ip
						if (!PluginAllowip::ipExists($this->ip)) {
							\Event::fire('desktop.login_ip_banned', [$guard->user()]);
							$msg = '您的IP ' . $this->ip . ' 不在允许访问之列!请不要尝试登陆!!!';
							return Resp::web(Resp::ERROR, $msg, 'location|' . route('dsk_lemon_home.login'), $request->only('adm_name'));
						}
					}
					*/

					// check is_enable
					$account = $guard->user();
					if ($account['is_enable'] == 'N') {
						return Resp::web(Resp::ERROR, '用户被禁用', 'location|' . route('dsk_lemon_home.login'), $request->only('adm_name'));
					}
				}
				/** @var PamAccount $user */
				$user = $guard->user();
				$guard->login($user, true);
				return Resp::web(Resp::SUCCESS, '登陆成功', 'location|' . route('dsk_lemon_home.cp'));
			}
			else {
				\Event::fire('auth.failed', [$credentials]);
				return Resp::web(Resp::ERROR, '登陆用户名密码不匹配', 'location|' . route('dsk_lemon_home.login'), $request->only('adm_name'));
			}
		}
		/** @var PamAccount $user */
		$user = $guard->user();
		if ($guard->check() && $user->account_type == PamAccount::ACCOUNT_TYPE_DESKTOP) {
			return Resp::web(Resp::SUCCESS, '已经登录', 'location|' . route('dsk_lemon_home.cp'));
		}
		return view('desktop.lemon_home.login');
	}


	/**
	 * 修改本账户密码
	 * @return \Illuminate\View\View
	 */
	public function getPassword()
	{
		return view('desktop.lemon_home.password');
	}

	/**
	 * 修改本账户密码
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postPassword(Request $request)
	{

		$validator = \Validator::make($request->all(), [
			'password'     => 'required|confirmed',
			'old_password' => 'required',
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->errors());
		}

		$old_password = $request->input('old_password');
		if ($old_password) {
			if (!PamAccount::checkPassword($this->pam, $old_password)) {
				return Resp::web(Resp::ERROR, '原密码错误!');
			}
		}


		$password = $request->input('password');
		PamAccount::changePassword(\Auth::id(), $password);
		\Auth::logout();
		return Resp::web(Resp::SUCCESS, '修改成功,请重新登录', 'location|' . route('dsk_lemon_home.login'));
	}

	/**
	 * 登出
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getLogout()
	{
		\Auth::logout();
		return Resp::web(Resp::SUCCESS, '退出登录', 'location|' . route('dsk_lemon_home.login'));
	}

	/**
	 * 控制面板
	 * @return \Illuminate\View\View
	 */
	public function getCp()
	{
		return $this->getWelcome();
	}

	/**
	 * 欢迎页面
	 * @return \Illuminate\View\View
	 */
	public function getWelcome()
	{
		$build     = FileHelper::get(app_path('build.md'));
		$buildHtml = nl2br($build);
		return view('desktop.lemon_home.welcome', [
			'html' => $buildHtml,
		]);
	}


	public function getTip()
	{
		return view('desktop.inc.tip');
	}

	public function getTest()
	{

	}
}


