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

class CreateUserStatusTable2021_05_20_164150 extends Migration
{
	protected $statusTable = 'user_status';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create($this->statusTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id', false, true)->default(0)->comment('归属用户id');
			$table->integer('operator_id', false, true)->default(0)->comment('操作人id');
			$table->integer('type', false, true)->default(0)->comment('操作类型');
			$table->string('relation', 100)->comment('关联模型');
			$table->integer('relation_id', false, true)->default(0)->comment('关联模型id');
			$table->integer('is_show', false, true)->default(1)->comment('是否展示');
			$table->string('remark', 255)->comment('备注');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['user_id'], 'user_id');
			$table->index(['operator_id'], 'operator_id');
			$table->index(['type'], 'type');
		});
		$tableName = idb()->getTablePrefix() . $this->statusTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '用户动态'");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->statusTable);
	}
}
