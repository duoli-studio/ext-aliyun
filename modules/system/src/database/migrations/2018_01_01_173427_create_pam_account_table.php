<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreatePamAccountTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('pam_account', function (Blueprint $table) {
			$table->increments('id')->comment('用户id');
			$table->string('username', 45)->comment('用户名');
			$table->string('mobile', 45);
			$table->string('email', 50)->comment('邮箱');
			$table->string('password', 45)->comment('密码');
			$table->string('password_key', 10)->comment('密码Key');
			$table->string('type', 20);
			$table->tinyInteger('is_enable')->comment('是否禁用');
			$table->string('disable_reason', 255)->comment('禁用原因');
			$table->dateTime('disable_start_at')->nullable()->comment('禁用开始时间');
			$table->dateTime('disable_end_at')->nullable()->comment('禁用结束时间');
			$table->integer('login_times')->comment('登录次数');
			$table->string('remember_token', 250)->comment('token');
			$table->dateTime('created_at')->nullable()->comment('创建时间');
			$table->dateTime('logined_at')->nullable()->comment('上次登录时间');
			$table->dateTime('updated_at')->nullable()->comment('修改时间');

			$table->primary('id','id_PRIMARY');
		});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pam_account');
	}
}
