<?php namespace System\Request\Backend;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use System\Models\Filters\PamFilter;
use System\Models\PamAccount;
use System\Models\PamRole;
use System\Models\PamRoleAccount;
use System\Pam\Action\Pam;

/**
 * 账户管理
 */
class PamController extends InitController
{


	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @return \Response
	 */
	public function index(Request $request)
	{
		$input = $request->all();
		$type  = $request->input('type', PamAccount::TYPE_BACKEND);
		$types = PamAccount::kvType();
		$items = PamAccount::filter($input, PamFilter::class)->paginateFilter();

		return view('system::backend.pam.index', [
			'items' => $items,
			'type'  => $type,
			'types' => $types,
			'roles' => PamRole::getLinear($type),
		]);
	}


	/**
	 * Show the form for creating a new resource.
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Response
	 * @throws \Throwable
	 */
	public function establish(Request $request)
	{
		$type = $request->input('type');

		if (is_post()) {
			$username  = $request->input('username');
			$password  = $request->input('password');
			$role_name = $request->input('role_name');

			/** @var Pam $actPam */
			$actPam = app('act.pam');
			if ($actPam->register($username, $password, $role_name)) {
				return Resp::web(Resp::SUCCESS, '用户添加成功', 'top_reload|1');
			}
			else {
				return Resp::web(Resp::ERROR, $actPam->getError());
			}
		}
		return view('system::backend.pam.establish', [
			'type'  => $type,
			'roles' => PamRole::getLinear($type, 'name'),
		]);
	}

	/**
	 * 设置密码
	 * @param Request $request
	 * @param         $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function password(Request $request, $id)
	{
		$pam = PamAccount::find($id);
		if (is_post()) {

			$validator = \Validator::make($request->all(), [
				'password' => [
					Rule::required(),
					Rule::confirmed(),
				],
			], []);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->errors());
			}

			$password = \Input::get('password');

			/** @var Pam $actPam */
			$actPam = app('act.pam');
			$actPam->setPam($this->getBeGuard()->user());
			if ($actPam->setPassword($pam, $password)) {
				return Resp::web(Resp::SUCCESS, '设置密码成功', 'top_reload|1');
			}
			else {
				return Resp::web(Resp::ERROR, $actPam->getError());
			}
		}
		return view('system::backend.pam.password', [
			'pam' => $pam,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postDestroy(Request $request)
	{
		$id      = $request->input('id');
		$account = PamAccount::find($id);

		\DB::transaction(function() use ($account) {
			// 删除 pam

			$id           = $account->account_id;
			$account_type = $account->account_type;
			PamAccount::destroy($id);

			// 删除 pam 附属资料
			if ($account_type == PamAccount::ACCOUNT_TYPE_DESKTOP) {
				AccountDesktop::destroy($id);
			}
			if ($account_type == PamAccount::ACCOUNT_TYPE_FRONT) {
				AccountFront::destroy($id);
			}
			if ($account_type == PamAccount::ACCOUNT_TYPE_DEVELOP) {
				AccountDevelop::destroy($id);
			}

			// 删除 role_account 关联
			PamRoleAccount::where('account_id', $id)->delete();
		});

		return AppWeb::resp(AppWeb::SUCCESS, '删除用户成功', 'location|' . route('dsk_pam_account.index', ['type' => $account['account_type']]));
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int $id
	 * @return \Response
	 */
	public function getEdit($id)
	{
		$item = PamAccount::info($id, true);
		return view('desktop.pam_account.item', [
			'account_type' => $item['account_type'],
			'item'         => $item,
			'roles'        => PamRole::getLinear($item['account_type']),
		]);
	}

	/*
	public function getAuth($id) {
		$user = PamAccount::info($id);
		if ($user['account_type'] != PamAccount::ACCOUNT_TYPE_FRONT) {
			return AppWeb::resp(AppWeb::ERROR, '用户类型不正确!');
		}

		// set cookie
		\Session::set('dsk_auth', SysCrypt::encode('desktop_id|' . \Auth::id() . ';front_id|' . $id));
		\Auth::logout();
		\Auth::loginUsingId($id);
		// show and redirect
		return AppWeb::resp(AppWeb::SUCCESS, '用户授权成功!', [
			'location' => route('home.cp'),
			'time'     => '3000',
		]);
	}
	*/

	/**
	 * 更新
	 * @param Request $request
	 * @param  int    $id
	 * @return \Response
	 */
	public function postEdit(Request $request, $id)
	{
		$pam = PamAccount::find($id);

		// 修改密码
		$password = $request->input('password');
		if ($password) {
			PamAccount::changePassword($id, $password);
		}

		// 更新附属信息
		$account_type = $pam['account_type'];
		if ($account_type == PamAccount::ACCOUNT_TYPE_DESKTOP) {
			AccountDesktop::where('account_id', $id)->update($request->input('desktop'));
		}
		if ($account_type == PamAccount::ACCOUNT_TYPE_FRONT) {
			$front = $request->input('front');
			if (isset($front['payword']) && $front['payword']) {
				AccountFront::changePayword($id, $front['payword']);
			}
			unset($front['payword'], $front['payword_confirmation']);
			AccountFront::where('account_id', $id)->update($front);
		}
		if ($account_type == PamAccount::ACCOUNT_TYPE_DEVELOP) {
			AccountDevelop::where('account_id', $id)->update($request->input('develop'));
		}

		// 更新角色id
		$role_id = $request->input('role_id');
		PamRoleAccount::where('account_id', $id)->update([
			'role_id' => $role_id,
		]);

		$account_type = $request->input('account_type');

		return AppWeb::resp(AppWeb::SUCCESS, '用户资料编辑成功', 'location|' . route('dsk_pam_account.index', ['type' => $account_type]));
	}

	public function postDisable(Request $request)
	{
		$accountId = $request->input('account_id');
		if (!$accountId) {
			return AppWeb::resp(AppWeb::ERROR, '您尚未选择用户!');
		}
		PamAccount::whereIn('account_id', $accountId)->update(['is_enable' => 'N']);
		return AppWeb::resp(AppWeb::SUCCESS, '状态修改成功', 'reload|1');
	}

	public function postEnable(Request $request)
	{
		$accountId = $request->input('account_id');
		if (!$accountId) {
			return AppWeb::resp(AppWeb::ERROR, '您尚未选择用户!');
		}
		PamAccount::whereIn('account_id', $accountId)->update(['is_enable' => 'Y']);
		return AppWeb::resp(AppWeb::SUCCESS, '状态修改成功', 'reload|1');
	}


	/**
	 * 改变账户状态
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postStatus(Request $request)
	{
		$field  = $request->input('field');
		$status = $request->input('status');
		$id     = $request->input('id');
		$user   = PamAccount::find($id);
		if ($user->hasRole('root')) {
			return AppWeb::resp(AppWeb::ERROR, '账号是超级管理员', 'reload|1');
		}
		PamAccount::where('account_id', $id)->update([
			$field => $status,
		]);
		return AppWeb::resp(AppWeb::SUCCESS, '状态修改成功', 'reload|1');
	}

	/**
	 * @return \Illuminate\View\View
	 */
	public function getLog()
	{
		$items = PamLog::orderBy('created_at', 'desc')->paginate($this->pagesize);
		return view('desktop.pam_account.log', [
			'items' => $items,
		]);
	}

	/**
	 * @param $id
	 * @return \Illuminate\View\View
	 */
	public function getAcl($id)
	{
		$acl  = BaseConfig::getCache('acl');
		$auth = array_get($acl, 'account_id_' . $id);
		if ($auth) {
			$auth = json_decode($auth);
		}
		else {
			$auth = [];
		}
		return view('desktop.pam_account.acl', [
			'id'   => $id,
			'auth' => ['auth' => $auth],
		]);
	}

	public function postAcl()
	{
		$id = \Input::get('account_id');
		if (!$id) {
			return AppWeb::resp(AppWeb::ERROR, '用户信息不存在');
		}
		$auth = json_encode(\Input::get('auth'));
		BaseConfig::configUpdate(['account_id_' . $id => $auth], 'acl');
		BaseConfig::reCache('acl');
		return AppWeb::resp(AppWeb::SUCCESS, '保存成功', 'reload|1');
	}


}
