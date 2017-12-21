<?php namespace System\Models;


use User\Rbac\Contracts\RbacPermissionContract;
use User\Rbac\Traits\RbacPermissionTrait;

/**
 * permission
 */
class PamPermission extends \Eloquent implements RbacPermissionContract
{

	use RbacPermissionTrait;

	protected $table = 'pam_permission';

	protected $fillable = [
		'name',
		'title',
		'description',
		'group',
		'module',
		'is_menu',
	];


}
