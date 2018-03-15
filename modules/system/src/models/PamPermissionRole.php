<?php namespace System\Models;

/**
 * 角色 & 权限表
 */
class PamPermissionRole extends \Eloquent
{
	protected $table = 'pam_permission_role';

	protected $fillable = [
		'permission_id',
		'role_id',
	];
}
