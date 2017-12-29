<?php namespace System\Request\Develop;

use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Poppy\Framework\Helper\RawCookieHelper;
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
		$tokenUrl       = route('system:api.token');
		$graphqlUrl     = route('system:web.graphql');
		$graphqlAuthUrl = route('system:web.graphql', 'backend');

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

		$token = '';
		if (RawCookieHelper::has('dev_dianjing#token')) {
			$token = RawCookieHelper::get('dev_dianjing#token');
		}
		return view('system::develop.system.status', [
			'token_url'         => $tokenUrl,
			'token'             => $token,
			'graphql_view'      => $graphqlUrl,
			'graphql_auth_view' => $graphqlAuthUrl,
		]);
	}
}
