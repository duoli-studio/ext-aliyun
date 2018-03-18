<?php namespace System\Request\Develop;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use System\Action\Pam;
use System\Models\PamAccount;

class PamController extends InitController
{
	public function login(Request $request)
	{
		if (is_post()) {
			$username = $request->input('username');
			$password = $request->input('password');

			/** @var Pam $pam */
			$pam = new Pam();
			if ($pam->loginCheck($username, $password, PamAccount::GUARD_DEVELOP, true)) {
				return Resp::web(Resp::SUCCESS, '登录成功！', 'location|' . route('system:develop.cp.cp'));
			}
			 
				return Resp::web(Resp::ERROR, $pam->getError());
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