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

class CreateArticleTable2021_04_16_094319 extends Migration
{
	protected $columnTable = 'article_column';
	protected $tagConfigTable = 'article_tag_config';
	protected $articleTable = 'article';
	protected $tagTable = 'article_tag';
	protected $commentTable = 'article_comment';
	protected $messageTable = 'message';
	protected $messageTextTable = 'message_text';
	protected $columnSubTable = 'article_column_sub';
	protected $praiseTable = 'article_praise';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//文章标签
		$this->schema->create($this->tagConfigTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name', 255)->default('')->comment('标签名称');
			$table->tinyInteger('sort', false, true)->default(0)->comment('排序');
			$table->tinyInteger('status', false, true)->default(1)->comment('状态0不可用1可用');
			$table->timestamps();
			$table->index(['status'], 'status');
			$table->index(['sort'], 'sort');
		});
		//文章专栏
		$this->schema->create($this->columnTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id', false, true)->default(0)->comment('用户id');
			$table->string('name', 255)->default('')->comment('栏目名称');
			$table->integer('article_num', false, true)->default(0)->comment('文章数量');
			$table->integer('read_num', false, true)->default(0)->comment('阅读次数');
			$table->integer('subscribe_num', false, true)->default(0)->comment('订阅数量');
			$table->integer('praise_num', false, true)->default(0)->comment('点赞次数');
			$table->timestamps();
			$table->index(['user_id'], 'user_id');
			$table->index(['name'], 'name');
		});
		//文章
		$this->schema->create($this->articleTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('column_id', false, true)->default(0)->comment('专栏id');
			$table->string('tag_ids', 255)->default('')->comment('标签列表');
			$table->integer('user_id', false, true)->default(0)->comment('用户id');
			$table->string('title', 255)->default('')->comment('文章标题');
			$table->longText('content')->default('')->comment('文章内容');
			$table->tinyInteger('comment_status', false, true)->default(0)->comment('是否开启评论0否1是');
			$table->tinyInteger('is_reprint', false, true)->default(0)->comment('是否来自转载0否1是');
			$table->string('reprint_url', 255)->default('')->comment('转载地址');
			$table->tinyInteger('home_thumbnail', false, true)->default(0)->comment('首张缩略图0否1是');
			$table->integer('read_num', false, true)->default(0)->comment('阅读次数');
			$table->integer('praise_num', false, true)->default(0)->comment('点赞次数');
			$table->tinyInteger('status', false, true)->default(0)->comment('状态0未审核1已审核2审核失败');
			$table->string('reason', 255)->default('')->comment('驳回原因');
			$table->timestamps();
			$table->index(['user_id'], 'user_id');
			$table->index(['column_id'], 'column_id');
			$table->index(['title'], 'title');
			$table->index(['status'], 'status');
		});
		//文章标签
		$this->schema->create($this->tagTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('tag_id', false, true)->default(0)->comment('专栏id');
			$table->integer('article_id', false, true)->default(0)->comment('文章id');
			$table->timestamps();
			$table->index(['article_id'], 'article_id');
			$table->index(['tag_id'], 'tag_id');
		});
		//评论
		$this->schema->create($this->commentTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('article_id', false, true)->default(0)->comment('文章id');
			$table->string('comment', 255)->default('')->comment('评论');
			$table->tinyInteger('status', false, true)->default(0)->comment('状态0不显示1显示');
			$table->timestamps();
			$table->index(['article_id'], 'article_id');
			$table->index(['status'], 'status');
		});
		//订阅
		$this->schema->create($this->columnSubTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('column_id', false, true)->default(0)->comment('栏目id');
			$table->integer('user_id', false, true)->default(0)->comment('订阅用户id');
			$table->integer('creater_id', false, true)->default(0)->comment('栏目作者id');
			$table->tinyInteger('status', false, true)->default(2)->comment('状态1创建2订阅0取消订阅');
			$table->integer('sub_time', false, true)->default(0)->comment('订阅时间');
			$table->timestamps();
			$table->index(['user_id'], 'user_id');
			$table->index(['creater_id'], 'creater_id');
			$table->index(['status'], 'status');
		});
		//点赞

		$this->schema->create($this->praiseTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('article_id', false, true)->default(0)->comment('文章id');
			$table->integer('user_id', false, true)->default(0)->comment('点赞用户id');
			$table->tinyInteger('status', false, true)->default(2)->comment('状态0取消点赞1点赞');
			$table->integer('praise_time', false, true)->default(0)->comment('点赞时间');
			$table->timestamps();
			$table->index(['article_id'], 'article_id');
			$table->index(['status'], 'status');
		});

		//消息
		$this->schema->create($this->messageTable, function (Blueprint $table) {
			$table->integerIncrements('id');
			$table->integer('from_id', false, true)->comment('发送者');
			$table->integer('to_id', false, true)->comment('接收者 0:代表用户都能接收');
			$table->integer('text_id', false, true);
			$table->string('type', 30)->comment('类别 announce:系统公告, remind:系统提醒, chat:用户消息');
			$table->string('target_type', 50)->comment('目标类型');
			$table->integer('target_id', false, true)->default(0);
			$table->string('target_url', 255)->default('');
			$table->tinyInteger('is_read')->default(2)->comment('状态 1:已读, 2:未读');
			$table->timestamps();
			$table->softDeletes();
			$table->index(['to_id', 'is_read'], 'to_id');
			$table->index(['to_id', 'created_at', 'is_read'], 'to_id_created_at');
			$table->index(['to_id', 'target_type', 'is_read'], 'to_id_target_type');
			$table->index(['target_type', 'is_read'], 'target_type');
			$table->index(['created_at', 'is_read'], 'created_at');
		});

		$this->schema->create($this->messageTextTable, function (Blueprint $table) {
			$table->integerIncrements('id');
			$table->string('title', 100)->default('')->comment('标题');
			$table->text('content')->comment('内容');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->tagConfigTable);
		$this->schema->dropIfExists($this->columnTable);
		$this->schema->dropIfExists($this->articleTable);
		$this->schema->dropIfExists($this->tagTable);
		$this->schema->dropIfExists($this->commentTable);
		$this->schema->dropIfExists($this->columnSubTable);
		$this->schema->dropIfExists($this->praiseTable);
		$this->schema->dropIfExists($this->messageTable);
		$this->schema->dropIfExists($this->messageTextTable);
	}
}
