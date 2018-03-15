<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePamLogTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pam_log', function(Blueprint $table) {
			$table->increments('id')->comment('id');
			$table->integer('account_id')->default(0)->comment('用户ID');
			$table->string('account_type', 50)->default('')->comment('用户类型');
			$table->string('type', 50)->default('')->comment('登录类型');
			$table->string('ip', 50)->default('')->comment('登录IP');
			$table->string('area_text', 50)->default('')->comment('地区方式');
			$table->string('area_name', 50)->default('')->comment('地区');
			$table->dateTime('created_at')->nullable()->comment('创建时间');
			$table->dateTime('updated_at')->nullable()->comment('修改时间');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pam_log', function(Blueprint $table) {
			//
		});
	}
}
