<?php namespace System\Request\Develop;

use Curl\Curl;
use Illuminate\Http\Request;
use Poppy\Framework\Application\Controller;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Classes\Traits\ViewTrait;
use Poppy\Framework\Helper\RawCookieHelper;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Pam\Action\Pam;


class CpController extends Controller
{
	use SystemTrait, ViewTrait;

	public function __construct()
	{
		$this->middleware(['web', 'auth:develop']);
		parent::__construct();
		\View::share([
			'menus' => $this->getModule()->developMenus(),
		]);
	}

	/**
	 * 开发者控制台
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('system::develop.cp.cp');
	}

	/**
	 * graphi 控制面板
	 * @param string $schema
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function graphi($schema = 'default')
	{
		if ($schema == 'default') {
			$schema = '';
		}
		$token = RawCookieHelper::get('dev_token#' . $schema);
		return view('system::graphql.graphiql', [
			'graphqlPath' => route('api.graphql', $schema),
			'token'       => $token,
		]);
	}

	public function api(Request $request)
	{
		$guard = $request->get('guard', 'web');
		if (!in_array($guard, ['backend', 'web'])) {
			return Resp::web(Resp::ERROR, '错误的 guard 类型');
		}

		$tokenUrl       = route('api.token', $guard);
		$graphqlUrl     = route('system:develop.cp.graphql', 'default');
		$graphqlAuthUrl = route('system:develop.cp.graphql', $guard);

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
		return view('system::develop.cp.api', [
			'token_url'         => $tokenUrl,
			'token'             => $token,
			'type'              => $guard,
			'cookie_key'        => $cookieKey,
			'graphql_view'      => $graphqlUrl,
			'graphql_auth_view' => $graphqlAuthUrl,
		]);
	}

	/**
	 * token
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function setToken()
	{
		$type      = \Input::get('type');
		$cookieKey = 'dev_token#' . $type;
		if (is_post()) {
			$token = \Input::get('token');
			if (!$token) {
				return Resp::web(Resp::ERROR, 'token 不存在');
			}
			RawCookieHelper::remove($cookieKey);
			RawCookieHelper::set($cookieKey, $token);
			return Resp::web(Resp::SUCCESS, '设置 token 成功', 'top_reload|1');
		}
		$token = RawCookieHelper::get($cookieKey);
		return view('system::develop.cp.set_token', compact('type', 'token'));
	}

	/**
	 * 文档地址
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function doc()
	{
		$type = \Input::get('type', 'system');
		return redirect(url('docs/' . $type));
	}
}
