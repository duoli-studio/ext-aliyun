<?php namespace System\Request\Backend;

use Illuminate\Http\Request;
use Poppy\Framework\Classes\Resp;
use System\Action\Role;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\RoleFilter;
use System\Models\PamAccount;
use System\Models\PamRole;

class RoleController extends InitController
{
	use SystemTrait;

	public function __construct()
	{
		parent::__construct();

		$types = PamAccount::kvType();
		\View::share(compact('types'));
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @return \Response
	 */
	public function index(Request $request)
	{
		$input         = $request->all();
		$type          = $input['type'] ?? PamAccount::TYPE_BACKEND;
		$input['type'] = $type;
		$items         = PamRole::filter($input, RoleFilter::class)->paginateFilter();

		return view('system::backend.role.index', compact('items', 'type'));
	}

	/**
	 * 编辑 / 创建
	 * @param Request $request
	 * @param null    $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function establish(Request $request, $id = null)
	{
		$role = $this->role();
		if (is_post()) {
			if ($role->establish($request->all(), $id)) {
				return Resp::web(Resp::SUCCESS, '创建成功', 'top_reload|1');
			}
			 
				return Resp::web(Resp::ERROR, $role->getError());
		}
		$id && $role->initRole($id) && $role->share();

		return view('system::backend.role.establish');
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int $id
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 * @throws \Exception
	 */
	public function delete($id)
	{
		/** @var Role $role */
		$role = $this->role();
		if (!$role->delete($id)) {
			return Resp::web(Resp::ERROR, $role->getError());
		}
		 
			return Resp::web(Resp::SUCCESS, '删除成功', 'top_reload|1');
	}

	/**
	 * 带单列表
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
	 */
	public function menu($id)
	{
		$role = PamRole::find($id);
		if (is_post()) {
			$perms   = (array) \Input::input('permission_id');
			$actRole = $this->role();
			if (!$actRole->savePermission($id, $perms)) {
				return Resp::web(Resp::SUCCESS, $actRole->getError());
			}
			 
				return Resp::web(Resp::SUCCESS, '保存会员权限配置成功!', 'reload|1');
		}
		$permission = (new Role())->permissions($id);
		if (!$permission) {
			return Resp::web(Resp::ERROR, '暂无权限信息！');
		}

		return view('system::backend.role.menu', compact('permission', 'role'));
	}

	/**
	 *
	 * @return Role
	 */
	private function role()
	{
		return (new Role())->setPam($this->getBeGuard()->user());
	}
}