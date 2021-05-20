<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class AddColumnIdToUserOperateLog2021_05_19_102338 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->table('user_operate_log', function (Blueprint $table) {
			$table->integer('column_id', false, true)->after('target_user_id')->default(0)->comment('专栏id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->table('user_operate_log', function (Blueprint $table) {
			$table->dropColumn('column_id');
		});
	}
}
