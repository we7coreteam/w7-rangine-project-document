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

class CreateVideoTable2021_06_23_150738 extends Migration
{
	protected $videoTable = 'video';
	protected $commentTable = 'video_comment';
	protected $praiseTable = 'video_praise';
	protected $carouselTable = 'video_carousel';
	protected $configTable = 'video_category_config';
	protected $categoryTable = 'video_category';
	protected $activityTable = 'video_activity';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create($this->videoTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('title', 100)->default('')->comment('视频标题');
			$table->string('cover', 255)->default('')->comment('视频封面');
			$table->string('url', 255)->default('')->comment('视频地址');
			$table->string('description', 255)->default('')->comment('视频简介');
			$table->string('time_length', 100)->default('')->comment('视频时长');
			$table->string('category_ids', 255)->default('')->comment('分类id');
			$table->integer('user_id', false, true)->default(0)->comment('归属用户id');
			$table->integer('play_num', false, true)->default(0)->comment('播放次数');
			$table->integer('praise_num', false, true)->default(0)->comment('点赞次数');
			$table->tinyInteger('is_reprint', false, true)->default(0)->comment('是否来自转载0否1是');
			$table->string('reprint_url', 255)->default('')->comment('转载地址');
			$table->tinyInteger('status', false, true)->default(0)->comment('状态0未审核1已审核2审核失败');
			$table->string('reason', 255)->default('')->comment('驳回原因');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['user_id'], 'user_id');
			$table->index(['status'], 'status');
		});
		$tableName = idb()->getTablePrefix() . $this->videoTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频'");

		$this->schema->create($this->commentTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('video_id', false, true)->default(0)->comment('视频id');
			$table->string('comment', 255)->default('')->comment('评论');
			$table->integer('user_id', false, true)->default(0)->comment('评论用户id');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['video_id'], 'video_id');
		});
		$tableName = idb()->getTablePrefix() . $this->commentTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频评论'");

		$this->schema->create($this->praiseTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('video_id', false, true)->default(0)->comment('视频id');
			$table->integer('user_id', false, true)->default(0)->comment('点赞用户id');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['video_id'], 'video_id');
			$table->index(['user_id'], 'user_id');
		});
		$tableName = idb()->getTablePrefix() . $this->praiseTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频点赞'");

		$this->schema->create($this->carouselTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name', 100)->default('')->comment('轮播名称');
			$table->string('url', 255)->default('')->comment('轮播链接');
			$table->string('image', 255)->default('')->comment('轮播图片');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
		});
		$tableName = idb()->getTablePrefix() . $this->carouselTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频轮播'");

		$this->schema->create($this->configTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name', 100)->default('')->comment('分类名称');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
		});
		$tableName = idb()->getTablePrefix() . $this->configTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频分类配置'");

		$this->schema->create($this->categoryTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('category_id', false, true)->default(0)->comment('分类id');
			$table->integer('video_id', false, true)->default(0)->comment('视频id');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
			$table->index(['video_id'], 'video_id');
			$table->index(['category_id'], 'category_id');
		});
		$tableName = idb()->getTablePrefix() . $this->categoryTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频分类'");

		$this->schema->create($this->activityTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name', 100)->default('')->comment('活动名称');
			$table->string('url', 255)->default('')->comment('活动链接');
			$table->string('image', 255)->default('')->comment('活动图片');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
		});
		$tableName = idb()->getTablePrefix() . $this->activityTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '视频活动'");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->videoTable);
		$this->schema->dropIfExists($this->commentTable);
		$this->schema->dropIfExists($this->praiseTable);
		$this->schema->dropIfExists($this->carouselTable);
		$this->schema->dropIfExists($this->configTable);
		$this->schema->dropIfExists($this->categoryTable);
		$this->schema->dropIfExists($this->activityTable);
	}
}
