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

class CreateUserFollowerTable2021_05_19_152154 extends Migration
{
	protected $followerTable = 'user_follower';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create($this->followerTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id', false, true)->default(0)->comment('用户id');
			$table->integer('follower_id', false, true)->default(0)->comment('粉丝id');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['user_id'], 'user_id');
			$table->index(['follower_id'], 'follower_id');
		});
        $tableName = idb()->getTablePrefix() . $this->followerTable;
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '用户粉丝'");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->followerTable);
	}
}
