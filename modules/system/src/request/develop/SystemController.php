<?php namespace System\Request\Develop;

use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use User\Pam\Action\Pam;

/**
 * 后端入口
 */
class SystemController extends Controller
{
	use SystemTrait, ViewTrait;


	public function status(Request $request)
	{
		$tokenUrl = route('system:api.token');
		if (is_post()) {
			$username = $request->input('username');
			$password = $request->input('password');

			/** @var Pam $pam */
			$pam = app('act.pam');
			if ($pam->loginCheck($username, $password, PamAccount::GUARD_DEVELOP, true)) {
				return Resp::web(Resp::SUCCESS, '登录成功！', 'location|' . route('system:develop.cp'));
			}
			else {
				return Resp::web(Resp::ERROR, $pam->getError());
			}
		}

		$guard = $this->getAuth()->guard(PamAccount::GUARD_DEVELOP)->user();
		return view('system::develop.system.status', [
			'token_url' => $tokenUrl
		]);
	}
}
