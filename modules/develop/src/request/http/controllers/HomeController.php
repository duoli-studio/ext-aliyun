<?php namespace App\Http\Controllers\Develop;


use App\Lemon\Dailian\Action\ActionAccount;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\System\SysAcl;
use App\Models\PamAccount;
use Illuminate\Http\Request;

class HomeController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_develop.auth', [
			'except' => [
				'getLogin',
				'postLogin',
			]
		]);
	}

	/**
	 * 字体
	 * @return \Illuminate\View\View
	 */
	public function getCp() {
		$menus = SysAcl::menu(PamAccount::ACCOUNT_TYPE_DEVELOP, null, true);
		return view('develop.home.cp', [
			'menus' => $menus,
		]);
	}

	public function getLogin() {
		if (\Auth::user() && \Auth::user()->account_type == PamAccount::ACCOUNT_TYPE_DEVELOP) {
			return AppWeb::resp(AppWeb::SUCCESS, '您已登录', [
				'location' => route('dev_home.cp'),
			]);
		}
		return view('develop.home.login');
	}

	public function postLogin(Request $request) {
		$account_name = $request->input('username');
		$password     = $request->input('password');
		$credentials  = [
			'account_name' => $account_name,
			'password'     => $password,
			'account_type' => PamAccount::ACCOUNT_TYPE_DEVELOP,
		];
		// login and redirect
		if (\Auth::attempt($credentials, false, true)) {
			$Account = new ActionAccount();
			$Account->setPam(\Auth::user());
			$Account->online();
			return AppWeb::resp(AppWeb::SUCCESS, '登录成功！', 'location|' . route('dev_home.cp'));
		} else {
			\Event::fire('auth.failed', [$credentials]);
			return AppWeb::resp(AppWeb::ERROR, '登录失败, 请重试！');
		}
	}

}