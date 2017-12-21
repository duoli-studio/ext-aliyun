<?php namespace User\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use User\Models\PamPermission;
use User\Models\PamRole;
use User\Classes\AclHelper;

/**
 * rbac
 * ---- 初始化
 * php artisan pam:rbac init
 */
class Rbac extends Command
{

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'pam:rbac 
		{do : actions in rbac}
		{--permission= : the permission need check}
		';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'rbac auth init handler.';


	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{

		$do = $this->argument('do');
		switch ($do) {
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
				$this->error('Please type right action![init|check]');
				break;
		}

	}

	private function checkPermission($permission)
	{
		if (PamPermission::where('name', $permission)->exists()) {
			$this->info('Permission [' . $permission . '] in table ');
		}
		else {
			$this->error('Permission [' . $permission . '] not in table, Please run `pam:rbac init` command');
		}
	}

	/**
	 * 初始化权限
	 */
	private function init()
	{
		/** @var Collection $slugs */
		$slugs = app('poppy')->slugs();
		if (!$slugs->count()) {
			$this->error('Poppy Slugs is empty, You can add module use artisan `make:poppy {module}`');
			return;
		}
		$bar = $this->output->createProgressBar(count($slugs));
		foreach ($slugs as $slug) {
			$this->initSlug($slug);
			$bar->advance();
		}
		$bar->finish();
	}


	/**
	 * 将权限赋值给指定的用户组
	 */
	private function assign()
	{
		$role            = $this->ask('Which role name want assign permission ?');
		$permission_type = $this->ask('Which permission type you want to get ?');
		$user            = PamRole::where('name', $role)->first();

		if (!$user) {
			$this->error('Role [' . $role . '] not exists in table !');
			return;
		}

		$permissions = PamPermission::where('module', $permission_type)->get();
		if (!$permissions) {
			$this->error('Permission type [' . $permission_type . '] has no permissions !');
			return;
		}
		$user->savePermissions($permissions);
		$user->flushPermissionRole();
		$this->info("\nSave [{$permission_type}] permission to role [{$role}] !");

	}

	/**
	 * 初始化 slug
	 * @param $slug
	 */
	private function initSlug($slug)
	{
		// get all permission
		$permission = AclHelper::permission($slug);
		$dropNum    = 0;
		if (!$permission) {
			$this->info("\nPoppy slug [{$slug}] has no permission");
			return;
		}

		// db permission
		$existsPermissions = PamPermission::where('module', $slug)->pluck('name')->toArray();
		$currentNum        = count($existsPermissions);

		// out of date permission drop
		$needDrop = array_diff($existsPermissions, array_keys($permission));
		if ($needDrop) {
			PamPermission::where('module', $slug)->whereIn('name', $needDrop)->delete();
			$dropNum = count($needDrop);
		}

		// insert db
		foreach ($permission as $route => $value) {
			PamPermission::updateOrCreate([
				'name' => $route,
			], [
				'name'        => $route,
				'title'       => $value['title'],
				'group'       => $value['group_title'],
				'is_menu'     => ((int) $value['menu']) ? 1 : 0,
				'module'      => $slug,
				'description' => '',
			]);
		}
		$endNum = PamPermission::where('module', $slug)->count();

		$createdNum = $endNum - $currentNum;
		$str        = '';
		if ($dropNum) {
			$str .= '[' . $dropNum . '] dropped;';
		}
		if ($createdNum) {
			$str .= '[' . $createdNum . '] created;';
		}
		$this->info("\nImport permission [{$slug}] success!" . $str);
	}
}


