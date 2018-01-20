<?php namespace System\Permission\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use System\Classes\Traits\SystemTrait;
use System\Models\PamPermission;
use System\Models\PamRole;
use System\Permission\Permission;

/**
 * Class PermissionCommand.
 */
class PermissionCommand extends Command
{
	use SystemTrait;

	protected $signature   = 'system:permission
		{do : The permission action to handle, allow <lists,init>}
		{--permission= : The permission need to check}
		';
	protected $description = 'Permission manage list.';

	/**
	 * @var string Display Key;
	 */
	private $key;

	/**
	 * Command Handler.
	 * @return bool
	 * @throws \Exception
	 */
	public function handle()
	{

		$action    = $this->argument('do');
		$this->key = $this->key($action);
		switch ($action) {
			case 'lists':
				$this->lists();
				break;
			case 'init':
				$this->init();
				break;
			case 'assign':
				$this->assign();
				break;
			case 'check';
				$permission = $this->option('permission');
				$this->checkPermission($permission);
				break;
			default:
				$this->error($this->key . ' Command Not Exists!');
				break;
		}

		return true;
	}

	public function lists()
	{
		$data = new Collection();
		$this->getPermission()->permissions()->each(function (Permission $permission) use ($data) {
			$data->push([
				$permission->type(),
				$permission->key(),
				$permission->description(),
			]);
		});
		$this->table(
			['Type', 'Identification', 'Description'],
			$data->toArray()
		);
	}

	/**
	 * @throws \Exception
	 */
	public function init()
	{
		// get all permission
		$permissions = $this->getPermission()->permissions();
		$dropNum     = 0;
		if (!$permissions) {
			$this->info($this->key . "No permission need import.");
			return;
		}

		// db permission
		$existsPermissions = PamPermission::where('name', $permissions->keys())->pluck('name')->toArray();
		$currentNum        = count($existsPermissions);

		// out of date permission drop
		$needDrop = array_diff($existsPermissions, $permissions->keys()->toArray());
		if ($needDrop) {
			PamPermission::whereIn('name', $needDrop)->delete();
			$dropNum = count($needDrop);
		}

		// insert db
		foreach ($permissions as $key => $permission) {
			PamPermission::updateOrCreate([
				'name' => $key,
			], [
				'name'        => $key,
				'title'       => $permission->description(),
				'type'        => $permission->type(),
				'group'       => $permission->group(),
				'module'      => $permission->module(),
				'root'        => $permission->root(),
				'description' => '',
			]);
		}
		$endNum = PamPermission::count();

		$createdNum = $endNum - $currentNum;
		$str        = '';
		if ($dropNum) {
			$str .= '`' . $dropNum . '` dropped;';
		}
		if ($createdNum) {
			$str .= '`' . $createdNum . '` created;';
		}
		$this->info($this->key . "Import permission Success! " . $str);
	}

	/**
	 * 将权限赋值给指定的用户组
	 */
	private function assign()
	{
		$name            = $this->ask('Which role you want assign permission ?');
		$permission_type = $this->ask('Which permission you want to get ?');
		$role            = PamRole::where('name', $name)->first();

		if (!$role) {
			$this->error($this->key . 'Role [' . $name . '] not exists in table !');
			return;
		}

		$permissions = PamPermission::where('type', $permission_type)->get();
		if (!$permissions) {
			$this->error($this->key . 'Permission type [' . $permission_type . '] has no permissions !');
			return;
		}
		$role->savePermissions($permissions);
		$role->flushPermissionRole();
		$this->info("\nSave [{$permission_type}] permission to role [{$name}] !");

	}


	private function checkPermission($permission)
	{
		if (PamPermission::where('name', $permission)->exists()) {
			$this->info($this->key . 'Permission `' . $permission . '` in table ');
		}
		else {
			$this->error($this->key . 'Permission `' . $permission . '` not in table');
		}
	}

	private function key($action)
	{
		return '[System:Permission (action:' . $action . ')] ';
	}

}
