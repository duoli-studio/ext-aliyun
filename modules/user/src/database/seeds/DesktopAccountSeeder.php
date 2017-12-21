<?php use Illuminate\Database\Seeder;
use Poppy\Backend\Models\PamAccount;
use Poppy\Backend\Models\PamRole;

class DesktopAccountSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 * @return void
	 */
	public function run()
	{
		if (!PamAccount::where('account_name', 'root')->exists()) {
			$roleId = PamRole::where('role_name', 'root')->value('id');
			PamAccount::register('root', '123456', PamAccount::ACCOUNT_TYPE_BACKEND, $roleId);
		}
	}
}
