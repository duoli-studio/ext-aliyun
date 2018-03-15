<?php namespace System\Rbac\Contracts;

interface RbacPermissionContract
{
	/**
	 * Many-to-Many relations with role model.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function roles();
}

