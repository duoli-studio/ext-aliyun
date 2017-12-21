<?php namespace User\Console;

use Poppy\Backend\Models\BaseConfig;
use User\Action\ActPam;
use User\Models\PamAccount;
use User\Models\PamRole;
use Illuminate\Console\Command;

/**
 * User
 */
class User extends Command
{

	/**
	 * 前端部署.
	 * @var string
	 */
	protected $signature = 'pam:user 
		{do : actions in "reset_pwd"}
		{--account= : Account Name}
		{--pwd= : Account password}
		';

	/**
	 * 描述
	 * @var string
	 */
	protected $description = 'user handler.';


	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle()
	{

		$do = $this->argument('do');
		switch ($do) {
			case 'reset_pwd':
				$account_name = $this->ask('Your account name?');
				if (PamAccount::accountNameExists($account_name)) {
					$pwd = $this->ask('Your aim password');
					$pam = PamAccount::getByAccountName($account_name);
					$Pam = (new ActPam())->setPam($pam);
					$Pam->setPassword($pwd);
					$this->info('Reset user password success');
				}
				else {
					$this->error('Your account not exists');
				}
				break;
			case 'create_account':
				$account_name = $this->option('account');
				if (!PamAccount::accountNameExists($account_name)) {
					$pwd = $this->option('pwd');
					$Pam = new ActPam();
					if ($Pam->register($account_name, $pwd, PamRole::BE_ROOT)) {
						$this->info('User ' . $account_name . ' created');
					}
					else {
						$this->error($Pam->getError());
					}
				}
				else {
					$this->error('user ' . $account_name . ' exists');
				}
				break;
			case 'init_role':
				$roles = [
					[
						'name'         => PamRole::FE_USER,
						'title'        => '用户',
						'account_type' => PamAccount::ACCOUNT_TYPE_USER,
						'is_system'    => BaseConfig::YES,
					],
					[
						'name'         => PamRole::BE_ROOT,
						'title'        => '超级管理员',
						'account_type' => PamAccount::ACCOUNT_TYPE_BACKEND,
						'is_system'    => BaseConfig::YES,
					],
				];
				foreach ($roles as $role) {
					if (!PamRole::where('name', $role['name'])->exists()) {
						PamRole::create($role);
					}
				}
				break;
			default:
				$this->error('Please type right action![reset_pwd]');
				break;
		}

	}
}
