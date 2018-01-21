<?php namespace System\Models\Filters;

use EloquentFilter\ModelFilter;
use System\Models\PamRoleAccount;

class PamFilter extends ModelFilter
{
	public function type($type)
	{
		return $this->where('type', $type);
	}

	public function id($id)
	{
		return $this->where('id', $id);
	}

	public function username($username)
	{
		return $this->where('username', $username);
	}

	public function mobile($mobile)
	{
		return $this->where('mobile', $mobile);
	}

	public function email($email)
	{
		return $this->where('email', $email);
	}

	public function role_id($role_id)
	{
		$account_ids = PamRoleAccount::where('role_id', $role_id)->pluck('account_id');
		return $this->whereIn('account_id', $account_ids);
	}
}