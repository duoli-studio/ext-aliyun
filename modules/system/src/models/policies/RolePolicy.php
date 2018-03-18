<?php namespace System\Models\Policies;

use System\Models\PamAccount;
use System\Models\PamRole;

class RolePolicy
{
	private $manage = 'backend:global.role.manage';

	public function before(PamAccount $pam, $ability)
	{
		if (!$pam->hasRole('root')) {
			return $pam->capable($this->manage);
		}

		return null;
	}

	/**
	 * 编辑
	 * @param PamAccount $pam
	 * @return bool
	 */
	public function create(PamAccount $pam)
	{
		return true;
	}

	/**
	 * 编辑
	 * @param PamAccount $pam
	 * @param PamRole    $role
	 * @return bool
	 */
	public function edit(PamAccount $pam, PamRole $role)
	{
		return true;
	}

	/**
	 * 保存权限
	 * @param PamAccount $pam
	 * @param PamRole    $role
	 * @return bool
	 */
	public function menu(PamAccount $pam, PamRole $role)
	{
		if ($role->name == PamRole::BE_ROOT) {
			return false;
		}

		return true;
	}

	/**
	 * 删除
	 * @param PamAccount $pam
	 * @param PamRole    $role
	 * @return bool
	 */
	public function delete(PamAccount $pam, PamRole $role)
	{
		if ($role->is_system) {
			return false;
		}

		return true;
	}
}