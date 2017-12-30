<?php namespace System\Console;


use Illuminate\Console\Command;
use System\Models\PamRole;


/**
 * 项目初始化
 */
class InstallCommand extends Command
{

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'system:install';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'Install system module.';


	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		// check
		if (PamRole::where('name', PamRole::BE_ROOT)->exists()) {
			$this->warn('You Already Installed!');
			return;
		}

		$this->line('Start Install Lemon Framework!');

		/* Role
		 -------------------------------------------- */
		$this->warn('Init UserRole Ing...');
		$this->call('system:user', [
			'do' => 'init_role',
		]);
		$this->info('Install User Roles Success');

		/* create root user
		 -------------------------------------------- */
		$this->warn('Create Root User...');
		$account = $this->ask('What is your super admin name?');
		$pwd     = $this->ask('What is your password?');
		$this->call('system:user', [
			'do'        => 'create_root',
			'--account' => $account,
			'--pwd'     => $pwd,
		]);

		/* permission
		 -------------------------------------------- */
		$this->warn('Init Rbac Permission...');
		$this->call('system:permission', [
			'do' => 'init',
		]);
		$this->info('Init Rbac Permission Success');
	}
}
