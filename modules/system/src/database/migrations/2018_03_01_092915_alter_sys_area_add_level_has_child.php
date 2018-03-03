<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSysAreaAddLevelHasChild extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sys_area', function(Blueprint $table) {
			$table->tinyInteger('has_child')->default(0)->comment('是否有子元素')->after('parent_id');
			$table->tinyInteger('level')->default(0)->comment('级别')->after('has_child');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sys_area', function(Blueprint $table) {
			$table->dropColumn(['has_child', 'level']);
		});
	}
}
