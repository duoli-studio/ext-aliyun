<?php namespace System\Request\Develop;

use Curl\Curl;
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

		$token     = '';
		$cookieKey = 'dev_dianjing#token';
		if (RawCookieHelper::has($cookieKey)) {
			$token = RawCookieHelper::get($cookieKey);

			// check token is valid
			$curl   = new Curl();
			$access = route('system:api.access');
			$curl->setHeader('x-requested-with', 'XMLHttpRequest');
			$curl->setHeader('Authorization', 'Bearer ' . $token);
			$curl->post($access);
			if ($curl->httpStatusCode === 401) {
				RawCookieHelper::remove($cookieKey);
				$token = '';
			}
		}
		return view('system::develop.system.status', [
			'token_url'         => $tokenUrl,
			'token'             => $token,
			'cookie_key'        => $cookieKey,
			'graphql_view'      => $graphqlUrl,
			'graphql_auth_view' => $graphqlAuthUrl,
		]);
	}
}
