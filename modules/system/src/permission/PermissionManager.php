<?php namespace System\Permission;

use Illuminate\Support\Collection;
use System\Classes\Traits\SystemTrait;
use System\Models\PamAccount;
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
	 * check permission
	 * @param $permission
	 * @param $guard
	 * @return bool
	 */
	public function check($permission, $guard)
	{
		/** @var PamAccount $user */
		$user = $this->getAuth()->guard($guard)->user();
		if (!$user) {
			return false;
		}

		return $user->capable($permission);
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
				$rootSlug  = $root['slug'] ?? '';
				$rootTitle = $root['title'] ?? '';
				if (!$rootSlug) {
					return;
				}
				$typeSlug = PamAccount::GUARD_BACKEND;
				if (strpos($rootSlug, ':') !== false) {
					list($typeSlug, $rootSlug) = explode(':', $rootSlug);
				}
				$groups = collect($root['groups'] ?? []);
				$groups->each(function ($group) use ($perms, $module, $typeSlug, $rootSlug, $rootTitle) {
					$groupSlug  = $group['slug'] ?? '';
					$groupTitle = $group['title'] ?? '';
					if (!$groupSlug) {
						return;
					}
					$permissions = collect($group['permissions'] ?? []);
					$permissions->each(
						function ($permission) use ($perms, $module, $typeSlug, $rootSlug, $groupSlug, $groupTitle, $rootTitle) {
							$permissionSlug = $permission['slug'] ?? '';
							if (!$permissionSlug) {
								return;
							}
							$permission['root_title']  = $rootTitle;
							$permission['group_title'] = $groupTitle;
							$permission['module']      = $module;
							$permission['root']        = $rootSlug;
							$permission['type']        = $typeSlug;
							$permission['group']       = $groupSlug;
							$id                        = "{$typeSlug}:{$rootSlug}.{$groupSlug}.{$permissionSlug}";
							$perms->put($id, new Permission($permission, $id));
						}
					);
				});
			});
		});

		return $perms;
	}

	/**
	 * Get default permission by guard
	 * @param $group
	 * @return Collection
	 */
	public function defaultPermissions($group)
	{
		$permissions = collect([]);
		$this->permissions()->each(function (Permission $permission) use ($permissions, $group) {
			if ($permission->type() == $group && $permission->isDefault()) {
				$permissions->push($permission->key());
			}
		});

		return $permissions;
	}
}
