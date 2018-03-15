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
	protected $key = '';

	/**
	 * @var string Module name
	 */
	protected $root = '';

	/**
	 * @var string Root Permission Title
	 */
	protected $rootTitle = '';

	/**
	 * @var string Permission group title
	 */
	protected $groupTitle = '';

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

	public function __construct($permission, $key)
	{
		$this->isDefault   = $permission['default'] ?? false;
		$this->description = $permission['description'] ?? '';
		$this->root        = $permission['root'] ?? '';
		$this->type        = $permission['type'] ?? '';
		$this->group       = $permission['group'] ?? '';
		$this->module      = $permission['module'] ?? '';
		$this->rootTitle   = $permission['root_title'] ?? '';
		$this->groupTitle  = $permission['group_title'] ?? '';
		$this->key         = $key;
	}

	public function key()
	{
		return $this->key;
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

	public function rootTitle()
	{
		return $this->rootTitle;
	}

	public function groupTitle()
	{
		return $this->groupTitle;
	}

	public function description()
	{
		return $this->description;
	}

	/**
	 * 权限转换成数组
	 * @return array
	 */
	public function toArray()
	{
		return [
			'is_default'  => $this->isDefault,
			'description' => $this->description,
			'root'        => $this->root,
			'type'        => $this->type,
			'group'       => $this->group,
			'module'      => $this->module,
			'root_title'  => $this->rootTitle,
			'group_title' => $this->groupTitle,
			'key'         => $this->key,
		];
	}
}
