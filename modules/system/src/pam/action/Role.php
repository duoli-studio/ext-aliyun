<?php namespace System\Pam\Action;

use Poppy\Framework\Validation\Rule;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
use System\Models\PamPermission;
use System\Models\PamPermissionRole;
use System\Models\PamRole;
use System\Models\PamRoleAccount;

class Role
{

	use SystemTrait;

	/** @var PamRole */
	protected $role;

	/** @var int Role id */
	protected $roleId;

	/**
	 * @var string
	 */
	protected $roleTable;

	public function __construct()
	{
		$this->roleTable = (new PamRole())->getTable();
	}

	/**
	 * 创建需求
	 * @param array    $data
	 * @param null|int $id
	 * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function establish($data, $id = null)
	{

		if (!$this->checkPam()) {
			return false;
		}

		$initDb = [
			'title'       => strval(array_get($data, 'title', '')),
			'name'        => strval(array_get($data, 'name', '')),
			'type'        => strval(array_get($data, 'guard', '')),
			'description' => strval(array_get($data, 'description', '')),
		];

		$rule = [
			'title' => [
				Rule::required(),
				Rule::unique($this->roleTable, 'title')->where(function($query) use ($id) {
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],
			'name'  => [
				Rule::required(),
				Rule::string(),
				Rule::alphaDash(),
				Rule::between(3, 15),
				Rule::unique($this->roleTable, 'name')->where(function($query) use ($id) {
					if ($id) {
						$query->where('id', '!=', $id);
					}
				}),
			],

			'type' => [
				Rule::required(),
				Rule::in([
					PamAccount::GUARD_BACKEND,
					PamAccount::GUARD_WEB,
					PamAccount::GUARD_DEVELOP,
				]),
			],
		];
		if ($id) {
			unset($rule['name'], $rule['type']);
		}
		$validator = \Validator::make($initDb, $rule, [], [
			'name'  => trans('system::role.db.name'),
			'title' => trans('system::role.db.title'),
			'type'  => trans('system::role.db.type'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		// init
		if ($id && !$this->initRole($id)) {
			return false;
		}

		if ($this->roleId) {
			if (!$this->pam->can('edit', $this->role)) {
				return $this->setError(trans('system::role.action.no_policy_to_update'));
			}
			// 编辑时候类型和名称不允许编辑
			unset($initDb['type'], $initDb['name']);
			$this->role->update($initDb);
		}
		else {
			if (!$this->pam->can('create', PamRole::class)) {
				return $this->setError(trans('system::role.action.no_policy_to_create'));
			}
			$this->role = PamRole::create($initDb);
		}
		return true;
	}


	/**
	 * 保存权限
	 * @param array $permission_ids 所有的权限列表
	 * @param int   $role_id        角色ID
	 * @return bool
	 */
	public function savePermission($role_id, $permission_ids)
	{
		if (!$this->checkPam()) {
			return false;
		}

		if (!$this->initRole($role_id)) {
			return false;
		}

		$validator = \Validator::make([
			'permission_ids' => $permission_ids,
		], [
			'permission_ids' => [
				Rule::required(),
			],
		], [], [
			'permission_ids' => trans('system::role.action.permissions'),
		]);
		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		if ($this->pam->can('savePermission', PamRole::class)) {
			return $this->setError(trans('system::role.action.no_policy_to_save_permission'));
		}

		$objPermissions = PamPermission::whereIn('id', $permission_ids)->where('type', $this->role->type)->get();
		if (!$objPermissions->count()) {
			return $this->setError(trans('system::role.action.permission_error'));
		}
		$this->role->savePermissions($objPermissions);
		return true;
	}

	public function initRole($id)
	{
		try {
			$this->role   = PamRole::findOrFail($id);
			$this->roleId = $this->role->id;
			return true;
		} catch (\Exception $e) {
			return $this->setError(trans('system::role.action.role_not_exists'));
		}
	}


	public function share()
	{
		\View::share(['item' => $this->role]);
	}


	/**
	 * 获取所有权限以及默认值
	 * @param $id
	 * @return array|mixed|\System\Permission\Permission
	 */
	public function permissions($id, $has_key = true)
	{
		$role        = PamRole::find($id);
		$permissions = $this->getPermission()->permissions();
		$type        = $role->type;

		$keys = $permissions->keys();

		$match = PamPermission::where('type', $type)->whereIn('name', $keys)->pluck('id', 'name');

		$collectPermission = collect();
		foreach ($permissions as $key => $permission) {
			$tmp = $permission->toArray();
			$id  = $match->get($tmp['key']);
			// 去掉本用户组不可控制的权限
			if (!$id) {
				continue;
			}
			$tmp['id'] = $match->get($tmp['key']);
			$collectPermission->put($key, $tmp);
		}

		$permission = [];
		$collectPermission->each(function($item, $key) use (&$permission, $role) {
			$root    = [
				'title'  => $item['root_title'],
				'root'   => $item['root'],
				'groups' => [],
			];
			$rootKey = $item['root'];
			if (!isset($permission[$rootKey])) {
				$permission[$rootKey] = $root;
			}
			$groupKey = $item['group'];
			$group    = [
				'group'       => $item['group'],
				'title'       => $item['group_title'],
				'permissions' => [],
			];
			if (!isset($permission[$rootKey]['groups'][$groupKey])) {
				$permission[$rootKey]['groups'][$groupKey] = $group;
			}

			$item['value'] = intval($role->hasPermission($key));

			unset(
				$item['is_default'],
				$item['root'],
				$item['group'],
				$item['module'],
				$item['key'],
				$item['root_title'],
				$item['type'],
				$item['group_title']
			);

			$permission[$rootKey]['groups'][$groupKey]['permissions'][] = $item;
		});

		if (!$has_key) {
			$p = [];
			foreach ($permission as $sp) {
				$pg = $sp;
				unset($pg['groups']);
				foreach ($sp['groups'] as $spg) {
					$pg['groups'][] = $spg;
				}
				$p[] = $pg;
			}
			$permission = $p;
		}
		return $permission;
	}


	/**
	 * 删除数据
	 * @param int $id
	 * @return bool
	 * @throws \Exception
	 */
	public function delete($id)
	{
		if (!$this->checkPam()) {
			return false;
		}

		if ($id && !$this->initRole($id)) {
			return false;
		}

		if (!$this->pam->can('delete', $this->role)) {
			return $this->setError(trans('system::role.action.no_policy_to_delete'));
		}

		if (PamRoleAccount::where('role_id', $this->roleId)->exists()) {
			return $this->setError(trans('system::role.action.role_has_account'));
		}

		// 删除权限
		PamPermissionRole::where('role_id', $this->roleId)->delete();

		// 删除角色
		$this->role->delete();
		return true;
	}

}