<?php namespace System\Request\Develop;

use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Pam\Action\Pam;


class PamController extends Controller
{
	use SystemTrait, ViewTrait;

	public function __construct()
	{
		$this->middleware(['web']);
		parent::__construct();
	}


	public function login(Request $request)
	{
		if (is_post()) {
			$username = $request->input('username');
			$password = $request->input('password');

			/** @var Pam $pam */
			$pam = app('act.pam');
			if ($pam->loginCheck($username, $password, PamAccount::GUARD_DEVELOP, true)) {
				return Resp::web(Resp::SUCCESS, '登录成功！', 'location|' . route('system:develop.cp.cp'));
			}
			else {
				return Resp::web(Resp::ERROR, $pam->getError());
			}
		}
		$guard = $this->getAuth()->guard(PamAccount::GUARD_DEVELOP)->user();
		// todo check guard permission
		if ($guard) {
			return Resp::web(Resp::SUCCESS, '您已登录', [
				'location' => route('system:develop.cp.cp'),
			]);
		}
		return view('system::develop.pam.login');
	}

}
