<?php namespace System\Permission;

use Illuminate\Support\Collection;
use System\Classes\Traits\SystemTrait;
use System\Module\Module;
use System\Permission\Repositories\PermissionRepository;

/**
 * Class PermissionManager.
 */
class PermissionManager
{
	use SystemTrait;

	/**
	 * @var PermissionRepository
	 */
	protected $repository;

	/**
	 * @param $identification
	 * @param $group
	 * @return bool
	 */
	public function check($identification, $group)
	{
		return true;
	}

	/**
	 * @return PermissionRepository
	 */
	public function repository(): PermissionRepository
	{
		if (!$this->repository instanceof PermissionRepository) {
			$this->repository = new PermissionRepository();
			$collection       = collect();
			$this->getModule()->enabled()->each(function (Module $module) use ($collection) {
				if ($module->offsetExists('permissions')) {
					$collection->put($module->slug(), $module->get('permissions'));
				}
			});
			$this->repository->initialize($collection);
		}

		return $this->repository;
	}


	/**
	 * Get all permissions.
	 * @return Collection|Permission[]
	 */
	public function permissions()
	{
		$perms = collect();
		$this->repository()->each(function ($permissions, $module) use ($perms) {
			collect($permissions)->each(function ($root) use ($perms, $module) {
				$rootSlug = $root['slug'] ?? '';
				if (!$rootSlug) {
					return;
				}
				$groups = collect($root['groups'] ?? []);
				$groups->each(function ($group) use ($perms, $module, $rootSlug) {
					$groupSlug = $group['slug'] ?? '';
					if (!$groupSlug) {
						return;
					}
					$permissions = collect($group['permissions'] ?? []);
					$permissions->each(function ($permission) use ($perms, $module, $rootSlug, $groupSlug) {
						$permissionSlug = $permission['slug'] ?? '';
						if (!$permissionSlug) {
							return;
						}
						$permission['module'] = $module;
						$permission['root']   = $rootSlug;
						$permission['group']  = $groupSlug;
						$id                   = "{$rootSlug}.{$groupSlug}.{$permissionSlug}";
						$perms->put($id, new Permission($permission, $id));
					});
				});
			});
		});
		return $perms;
	}
}
