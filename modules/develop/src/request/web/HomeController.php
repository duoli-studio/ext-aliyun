<?php namespace Develop\Request\Web;


use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Pam\Action\Pam;

class HomeController extends Controller
{

	use SystemTrait;

	/**
	 * 字体
	 * @return \Illuminate\View\View
	 */
	public function cp()
	{
		return view('develop::home.cp', [
			'menus' => $this->getModule()->navigation(),
		]);
	}

	public function login(Request $request)
	{
		if (is_post()) {
			$username = $request->input('username');
			$password = $request->input('password');

			/** @var Pam $pam */
			$pam = app('act.pam');
			if ($pam->loginCheck($username, $password, PamAccount::GUARD_DEVELOP, true)) {
				return Resp::web(Resp::SUCCESS, '登录成功！', 'location|' . route('develop:home.cp'));
			}
			else {
				return Resp::web(Resp::ERROR, $pam->getError());
			}
		}
		$guard = $this->getAuth()->guard(PamAccount::GUARD_DEVELOP)->user();
		// todo check guard permission
		if ($guard) {
			return Resp::web(Resp::SUCCESS, '您已登录', [
				'location' => route('develop:home.cp'),
			]);
		}
		return view('develop::home.login');
	}

}