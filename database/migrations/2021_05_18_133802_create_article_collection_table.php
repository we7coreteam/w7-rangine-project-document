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

class CreateArticleCollectionTable2021_05_18_133802 extends Migration
{
	protected $collectionTable = 'article_collection';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create($this->collectionTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('article_id', false, true)->default(0)->comment('文章id');
			$table->integer('user_id', false, true)->default(0)->comment('收藏用户id');
			$table->tinyInteger('status', false, true)->default(1)->comment('状态0取消收藏1收藏');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['article_id'], 'article_id');
			$table->index(['user_id'], 'user_id');
			$table->index(['status'], 'status');
		});
		$tableName = idb()->getTablePrefix() . $this->collectionTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '文章收藏'");

		$this->schema->table('article', function (Blueprint $table) {
			$table->integer('collection_num', false, true)->default(0)->comment('收藏数')->after('praise_num');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->collectionTable);
		$this->schema->table('article', function (Blueprint $table) {
			$table->dropColumn(['collection_num']);
		});
	}
}
