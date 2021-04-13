<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class AlterUserTable2021_04_09_100433 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('user', function (Blueprint $table) {
			//
			$table->string('avatar')->default('')->after('userpass')->comment('用户头像');
			$table->string('company')->default('')->after('group_id')->comment('公司和职称');
			$table->string('resume')->default('')->after('company')->comment('个人简历');
			$table->string('skill')->default('')->after('resume')->comment('技能');
			$table->string('address')->default('')->after('skill')->comment('所在城市');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('user', function (Blueprint $table) {
			//
			$table->dropColumn(['company','resume','skill','address']);
		});
	}
}
