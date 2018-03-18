<?php namespace System\Models;

use System\Rbac\Contracts\RbacPermissionContract;
use System\Rbac\Traits\RbacPermissionTrait;

/**
 * permission
 */
class PamPermission extends \Eloquent implements RbacPermissionContract
{
	use RbacPermissionTrait;

	protected $table = 'pam_permission';

	public $timestamps = false;

	protected $fillable = [
		'name',
		'title',
		'description',
		'group',
		'root',
		'module',
		'type',
	];
}