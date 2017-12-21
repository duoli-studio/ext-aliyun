<?php use Illuminate\Database\Seeder;
use Poppy\Backend\Models\PamRole;

class PamRoleSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 * @return void
	 */
	public function run()
	{
		if (!PamRole::where('role_name', 'root')->exists()) {
			PamRole::create([
				'role_name'    => 'root',
				'role_title'   => '超级管理员',
				'account_type' => \Poppy\Backend\Models\PamAccount::ACCOUNT_TYPE_BACKEND,
			]);
		}
	}
}
