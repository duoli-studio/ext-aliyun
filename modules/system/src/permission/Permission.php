<?php namespace System\Permission;

use System\Models\PamAccount;
use System\Permission\Repositories\PermissionRepository;

/**
 * Class PermissionManager.
 */
class Permission
{

	/**
	 * @var PermissionRepository
	 */
	protected $isDefault = false;

	/**
	 * @var string permission id
	 */
	protected $id = '';

	/**
	 * @var string Module name
	 */
	protected $root = '';

	/**
	 * @var string Group name
	 */
	protected $group = '';

	/**
	 * @var string Module name
	 */
	protected $module = '';

	/**
	 * @var string permission type
	 */
	protected $type = PamAccount::GUARD_BACKEND;

	/**
	 * @var string Permission description;
	 */
	protected $description = '';

	public function __construct($permission, $id)
	{
		$this->isDefault   = $permission['default'] ?? false;
		$this->description = $permission['description'] ?? '';
		$this->root        = $permission['root'] ?? '';
		$this->type        = $permission['type'] ?? '';
		$this->group       = $permission['group'] ?? '';
		$this->module      = $permission['module'] ?? '';
		$this->id          = $id;
	}

	public function id()
	{
		return $this->id;
	}

	public function type()
	{
		return $this->type;
	}

	public function isDefault()
	{
		return $this->isDefault;
	}

	public function root()
	{
		return $this->root;
	}

	public function group()
	{
		return $this->group;
	}

	public function module()
	{
		return $this->module;
	}


	public function description()
	{
		return $this->description;
	}
}
