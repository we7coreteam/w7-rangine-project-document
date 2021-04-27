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

class CreateArticleCommentPraiseTable2021_04_27_165716 extends Migration
{
	protected $commentPraiseTable = 'article_comment_praise';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create($this->commentPraiseTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('article_id', false, true)->default(0)->comment('文章id');
			$table->integer('comment_id', false, true)->default(0)->comment('评论id');
			$table->integer('user_id', false, true)->default(0)->comment('点赞用户id');
			$table->tinyInteger('status', false, true)->default(2)->comment('状态0取消点赞1点赞');
			$table->integer('praise_time', false, true)->default(0)->comment('点赞时间');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['article_id'], 'article_id');
			$table->index(['comment_id'], 'comment_id');
			$table->index(['user_id'], 'user_id');
			$table->index(['status'], 'status');
		});
		$tableName = idb()->getTablePrefix() . $this->commentPraiseTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '评论点赞'");

		$this->schema->table('article_comment', function (Blueprint $table) {
			$table->integer('praise_num', false, true)->default(0)->comment('点赞次数')->after('status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->commentPraiseTable);
		$this->schema->table('article_comment', function (Blueprint $table) {
			$table->dropColumn(['praise_num']);
		});
	}
}
