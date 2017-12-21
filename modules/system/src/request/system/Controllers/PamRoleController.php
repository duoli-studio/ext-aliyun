<?php namespace System\Request\Backend\Controllers;

use App\Http\Requests\Desktop\PamRoleRequest;
use App\Lemon\Dailian\Application\App\AppWeb;
use App\Lemon\Repositories\Application\Rbac\Helper\RbacHelper;
use App\Lemon\Repositories\Sour\LmUtil;
use App\Lemon\Repositories\System\SysAcl;
use App\Models\PamAccount;
use App\Models\PamRole;
use App\Models\PamRoleAccount;
use Illuminate\Http\Request;

/**
 * 角色管理
 * Class PamRoleController
 * @package App\Http\Controllers\Desktop
 */
class PamRoleController extends InitController {

	public function __construct(Request $request) {
		parent::__construct($request);
		$this->middleware('lm_desktop.auth');
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @return \Response
	 */
	public function getIndex(Request $request) {
		$type  = $request->input('type', PamAccount::ACCOUNT_TYPE_DESKTOP);
		$roles = PamRole::where('account_type', $type)
			->orderBy('created_at', 'desc')
			->paginate($this->pagesize);
		return view('desktop.pam_role.index', [
			'roles' => $roles,
			'type'  => $type,
		]);
	}

	public function getCreate() {
		return view('desktop.pam_role.item');
	}

	/**
	 * Store a newly created resource in storage.
	 * @param PamRoleRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postCreate(PamRoleRequest $request) {
		$input = $request->all();
		$id    = PamRole::create($input);
		if ($id) {
			return AppWeb::resp(AppWeb::SUCCESS, '创建成功', 'location|' . route('dsk_pam_role.index', ['type' => $input['account_type']]));
		} else {
			return AppWeb::resp(AppWeb::ERROR, '创建失败');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postDestroy($id) {
		$roleName = PamRole::where('id', $id)->value('role_name');
		if ($roleName == 'root') {
			return AppWeb::resp(AppWeb::ERROR, '超级用户不能被删除');
		}
		$count = PamRoleAccount::whereRaw('role_id= ? ', [$id])->count();
		if ($count) {
			return AppWeb::resp(AppWeb::ERROR, '存在用户, 不得删除');
		} else {
			$role = PamRole::find($id);
			PamRole::destroy($id);
			return AppWeb::resp(AppWeb::SUCCESS, '角色已经删除', 'location|' . route('dsk_pam_role.index', ['type' => $role['account_type']]));
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param  int $id
	 * @return \Response
	 */
	public function getEdit($id) {
		return view('desktop.pam_role.item', [
			'item' => PamRole::findOrFail($id),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param  int    $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function postEdit(Request $request, $id) {
		PamRole::where('id', $id)->update($request->except(['_token', '_method']));
		$accountType = PamRole::info($id, 'account_type', false);
		return AppWeb::resp(AppWeb::SUCCESS, '更新成功', 'location|' . route('dsk_pam_role.index', ['type' => $accountType]));
	}


	/**
	 * 验证
	 * @param Request  $request
	 * @param null|int $id
	 */
	public function postCheck(Request $request, $id = null) {
		$Role = PamRole::where('role_name', $request->input('role_name'));
		if ($id) {
			$Role->where('role_id', '!=', $id);
		}
		$role_id = $Role->value('role_id');
		LmUtil::av($role_id);
	}


	/**
	 * 带单列表
	 * @param $role_id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function getMenu($role_id) {
		$role        = PamRole::find($role_id);
		$accountType = $role->account_type;
		$permission  = RbacHelper::permission($accountType);
		$perms       = $role->perms();
		if (!$permission) {
			return AppWeb::resp(AppWeb::ERROR, '暂无权限信息！', 'location|' . route('dsk_home.tip'));
		}
		return view('desktop.pam_role.menu', [
			'permission' => $permission,
			'role'       => $role,
			'perms'      => $perms,
		]);
	}

	/**
	 * 更新会员组配置菜单成功
	 * @param Request $request
	 * @param         $role_id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postMenu(Request $request, $role_id) {
		$role = PamRole::find($role_id);
		$key  = $request->input('key');
		if (!$key) {
			$perms = [];
		} else {
			$perms = array_keys($key);
		}
		$role->savePermissions($perms);
		$role->flushPermissionRole();
		SysAcl::reCache();
		return AppWeb::resp(AppWeb::SUCCESS, '保存会员权限配置成功!', 'reload|1');
	}


}

