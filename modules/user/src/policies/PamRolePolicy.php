<?php namespace User\Policies;


use User\Models\PamAccount;
use User\Models\PamRole;

class PamRolePolicy
{

	/**
	 * @param PamAccount $account
	 * @param PamRole    $role
	 * @return bool
	 */
	public function menu(PamAccount $account, PamRole $role)
	{
		if ($role->role_name == 'root') {
			return false;
		}
		else {
			return $account->capable('dsk_pam_role.edit');
		}
	}

	public function destroy(PamAccount $account, PamRole $role)
	{
		if ($role->is_system) {
			return false;
		}
		else {
			return $account->capable('dsk_pam_role.destroy');
		}
	}

	public function edit(PamAccount $account, PamRole $role)
	{
		return $account->capable('dsk_pam_role.edit');
	}

	public function create(PamAccount $account, PamRole $role)
	{
		return $account->capable('dsk_pam_role.create');
	}
}
