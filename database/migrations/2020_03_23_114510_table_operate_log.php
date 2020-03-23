<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class TableOperateLog extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('user_operate_log', function (Blueprint $table) {
			$table->integer('target_user_id')->after('chapter_id')->nullable()->default(0)->comment('目标用户id,比如文档转让的目标用户id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('user_operate_log', function (Blueprint $table) {
			$table->dropColumn('target_user_id');
		});
	}
}
