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
class DevController extends Controller
{
	use SystemTrait, ViewTrait;

	/**
	 * 字体
	 * @return \Illuminate\View\View
	 */
	public function cp()
	{
		return view('system::develop.cp', [
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
				return Resp::web(Resp::SUCCESS, '登录成功！', 'location|' . route('system:develop.cp'));
			}
			else {
				return Resp::web(Resp::ERROR, $pam->getError());
			}
		}
		$guard = $this->getAuth()->guard(PamAccount::GUARD_DEVELOP)->user();
		// todo check guard permission
		if ($guard) {
			return Resp::web(Resp::SUCCESS, '您已登录', [
				'location' => route('system:develop.cp'),
			]);
		}
		return view('system::develop.login');
	}

	public function api(Request $request)
	{
		$guard = $request->get('guard', 'web');
		if (!in_array($guard, ['backend', 'web'])) {
			return Resp::web(Resp::ERROR, '错误的 guard 类型');
		}

		$tokenUrl       = route('api.token', $guard);
		$graphqlUrl     = route('system:develop.graphql', 'default');
		$graphqlAuthUrl = route('system:develop.graphql', $guard);

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
		$cookieKey = 'dev_token#' . $guard;
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
		return view('system::develop.api', [
			'token_url'         => $tokenUrl,
			'token'             => $token,
			'cookie_key'        => $cookieKey,
			'graphql_view'      => $graphqlUrl,
			'graphql_auth_view' => $graphqlAuthUrl,
		]);
	}

	public function phpinfo()
	{
		return view('system::develop.phpinfo');
	}
}
