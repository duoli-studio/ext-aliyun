<?php namespace User\Console;


use Illuminate\Console\Command;
use User\Models\PamAccount;
use User\Models\PamRole;


/**
 * 项目初始化
 */
class Init extends Command
{

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'pam:init';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'init project.';


	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		// check
		if (PamRole::where('role_name', PamRole::BE_ROOT)->exists()) {
			$this->warn('You Already Install Framework, Do not Init Again!');
			return;
		}

		$this->line('Start Install Lemon Framework!');

		// user role
		$this->warn('Init UserRole Ing...');
		$this->call('lemon:pam', [
			'do' => 'init_role',
		]);
		$this->info('Install User Roles Success');

		// create root user
		$this->warn('Create Root User...');
		$account = $this->ask('What is your super admin name?');
		$pwd     = $this->ask('What is your password?');
		$this->call('lemon:pam', [
			'do'        => 'create_account',
			'--account' => $account,
			'--pwd'     => $pwd,
		]);

		$this->warn('Init Rbac Permission...');
		$this->call('lemon:rbac', [
			'do'     => 'init',
			'--type' => PamAccount::ACCOUNT_TYPE_BACKEND,
		]);
		$this->call('lemon:rbac', [
			'do'     => 'init',
			'--type' => PamAccount::ACCOUNT_TYPE_USER,
		]);
		$this->info('Init Rbac Permission Success');
	}
}
