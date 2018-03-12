<?php namespace System\Request\ApiV1\Backend;


use Poppy\Framework\Application\ApiController;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use System\Action\Role as ActRole;
use System\Classes\Traits\SystemTrait;
use System\Models\Filters\RoleFilter;
use System\Models\PamAccount;
use System\Models\PamRole;
use System\Models\Resources\RoleResource;


class RoleController extends ApiController
{
	use SystemTrait;


	/**
	 * @api                   {post} api_v1/backend/system/role/lists [O]角色-列表
	 * @apiVersion            1.0.0
	 * @apiName               PamRoleList
	 * @apiGroup              Pam
	 * @apiPermission         backend
	 * @apiParam   {Integer}  [field]             角色ID
	 * @apiParam   {Integer}  [kw]                角色ID
	 * @apiParam   {Integer}  type                角色类型 [user|用户;backend|后台;develop|开发者]
	 * @apiParam   {Integer}  [page]              分页
	 * @apiParam   {Integer}  [size]              页数
	 * @apiSuccess {Object[]} list                列表
	 * @apiSuccess {Integer}  list.id             ID
	 * @apiSuccess {String}   list.name           标识
	 * @apiSuccess {String}   list.title          标题
	 * @apiSuccess {String}   list.type           角色类型
	 * @apiSuccess {String}   list.description    角色描述
	 * @apiSuccess {Boolean}  list.can_permission 是否可以编辑权限
	 * @apiSuccess {Boolean}  list.can_delete     是否可以删除
	 * @apiSuccessExample     list
	 * {
	 *     "list" : [
	 *         {
	 *             "id": 5,
	 *             "name": "root",
	 *             "title": "超级管理员",
	 *             "type": "backend",
	 *             "description": "",
	 *             "can_permission": false,
	 *             "can_delete": false
	 *         }
	 *     ],
	 *     "pagination": {
	 *         "total": 2,
	 *         "page": 1,
	 *         "size": 20,
	 *         "pages": 1
	 *     }
	 * }
	 */
	public function lists()
	{
		$input = \Input::all();
		$field = data_get($input, 'field');
		$kw    = data_get($input, 'kw');
		if ($field && $kw) {
			$input[$field] = $kw;
		}
		/** @var PamAccount $pam */
		$pam      = $this->getJwtBeGuard()->user();
		$Db       = PamRole::filter($input, RoleFilter::class);
		return PamRole::paginationInfo($Db, function($item) use ($pam) {
			$role                   = (new RoleResource($item))->toArray(app('request'));
			$role['can_permission'] = $pam->can('menu', $item);
			$role['can_delete']     = $pam->can('delete', $item);
			return $role;
		});
	}

	/**
	 * @api                  {get} api_v1/backend/system/role/permissions [O]角色-权限
	 * @apiVersion           1.0.0
	 * @apiName              PamRolePermissions
	 * @apiGroup             Pam
	 * @apiPermission        backend
	 * @apiParam   {Integer} role_id  角色ID
	 * @apiSuccess {String}  title                           描述
	 * @apiSuccess {String}  root                            根
	 * @apiSuccess {String}  groups                          所有分组
	 * @apiSuccess {String}  groups.group                    分组
	 * @apiSuccess {String}  groups.title                    标题
	 * @apiSuccess {String}  groups.permissions              所有权限
	 * @apiSuccess {String}  groups.permissions.description  描述
	 * @apiSuccess {String}  groups.permissions.id           权限ID
	 * @apiSuccess {String}  groups.permissions.value        选中值
	 * @apiSuccessExample    data:
	 * [
	 *     {
	 *         "title": "全局",
	 *         "root": "global",
	 *         "groups": [
	 *             {
	 *                 "group": "addon",
	 *                 "title": "插件权限",
	 *                 "permissions": [
	 *                     {
	 *                         "description": "全局插件管理权限",
	 *                         "id": 1,
	 *                         "value": 0
	 *                     }
	 *             },
	 *         ]
	 *     }
	 * ]
	 */
	public function permissions()
	{
		$validator = \Validator::make($this->getRequest()->all(), [
			'role_id' => Rule::required(),
		]);
		if ($validator->fails()) {
			return Resp::web(Resp::ERROR, $validator->messages(), 'json|1');
		}

		$role_id = $this->getRequest()->get('role_id');

		$Role = (new ActRole);
		if (!($permission = $Role->permissions($role_id, false))) {
			return Resp::web(Resp::ERROR, $Role->getError());
		}
		return Resp::web(Resp::SUCCESS, '获取成功', $permission);
	}

	/**
	 * @api                 {get} api_v1/backend/system/role/permissions_store [O]角色-权限保存
	 * @apiVersion          1.0.0
	 * @apiName             PamRolePermissionsStore
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {Integer}  role_id     角色ID
	 * @apiParam {String}   permission  权限ID集合, 可用 ',' 分隔
	 */
	public function permissionsStore()
	{
		$input      = \Input::all();
		$permission = $input['permission'] ?? null;
		$role_id    = $input['role_id'] ?? null;

		$Role = (new ActRole())->setPam($this->getJwtBeGuard()->user());
		if (!$Role->savePermission($role_id, $permission)) {
			return Resp::web(Resp::ERROR, $Role->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}

	/**
	 * @api                 {get} api_v1/backend/system/role/establish [O]角色-C2E
	 * @apiVersion          1.0.0
	 * @apiName             PamRoleEstablish
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {Integer}  role_id       角色ID
	 * @apiParam {Integer}  name          标识
	 * @apiParam {Integer}  title         名称
	 * @apiParam {Integer}  guard         角色类型 [user|用户;backend|后台;develop|开发者]
	 * @apiParam {Integer}  description   描述
	 */
	public function establish()
	{
		$all = \Input::all();
		$id  = \Input::get('id', null);
		$Role = (new ActRole())->setPam($this->getJwtBeGuard()->user());
		if (!$Role->establish($all, $id)) {
			return Resp::web(Resp::ERROR, $Role->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}

	/**
	 * @api                 {get} api_v1/backend/system/role/do [O]角色-Do
	 * @apiVersion          1.0.0
	 * @apiName             PamRoleDo
	 * @apiGroup            Pam
	 * @apiPermission       backend
	 * @apiParam {Integer}  id            角色ID
	 * @apiParam {String}   action        标识
	 */
	public function do()
	{
		$id  = \Input::get('id', null);

		$Role = (new ActRole())->setPam($this->getJwtBeGuard()->user());
		if (!$Role->delete($id)) {
			return Resp::web(Resp::ERROR, $Role->getError());
		}
		else {
			return Resp::web(Resp::SUCCESS, '操作成功');
		}
	}
}
