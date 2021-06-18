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

class CreateDocumentHistoryTable2021_06_17_094044 extends Migration
{
	protected $historyTable = 'document_history';
	protected $chapterTable = 'document_history_chapter';
	protected $contentTable = 'document_history_chapter_content';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create($this->historyTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('document_id', false, true)->default(0)->comment('所属文档id');
			$table->string('name', 30)->comment('名称');
			$table->integer('creator_id', false, true)->default(0)->comment('编辑人id');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
		});
		$tableName = idb()->getTablePrefix() . $this->historyTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '文档历史版本'");

		$this->schema->create($this->chapterTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('chapter_id', false, true)->default(0);
			$table->integer('parent_id', false, true)->default(0);
			$table->string('name', 30)->comment('名称');
			$table->integer('history_id', false, true)->default(0)->comment('版本id');
			$table->integer('document_id', false, true)->default(0)->comment('所属文档id');
			$table->integer('sort', false, true)->default(0)->comment('排序');
			$table->tinyInteger('is_dir', false, true)->nullable()->default(0)->comment('当前章节是否是目录 1是 0否');
			$table->tinyInteger('levels', false, true)->default(0);
			$table->integer('default_show_chapter_id', false, true)->nullable()->default(0)->comment('默认显示的章节');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
		});
		$tableName = idb()->getTablePrefix() . $this->chapterTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '文档历史版本章节'");

		$this->schema->create($this->contentTable, function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('history_chapter_id', false, true)->default(0);
			$table->longText('content')->nullable();
			$table->tinyInteger('layout')->comment('章节格式 1 markdown 2 富文本');
			$table->integer('created_at', false, true)->default(0);
			$table->integer('updated_at', false, true)->default(0);
		});
		$tableName = idb()->getTablePrefix() . $this->contentTable;
		\Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$tableName}` COMMENT '文档历史版本章节内容'");

		$this->schema->table('document', function (Blueprint $table) {
			$table->tinyInteger('is_history')->default(0)->after('is_public')->comment('是否开启历史记录 1开启0关闭');
			$table->integer('browse_num', false, true)->default(0)->after('is_history')->comment('浏览次数');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists($this->historyTable);
		$this->schema->dropIfExists($this->chapterTable);
		$this->schema->dropIfExists($this->contentTable);
		$this->schema->table('document', function (Blueprint $table) {
			$table->dropColumn('is_history');
		});
	}
}
