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

class CreateDocumentChapterRecord extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create('document_chapter_record', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('chapter_id')->default(0)->comment('章节ID');
			$table->longText('record')->default('')->comment('数据记录');
			$table->index('chapter_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists('document_chapter_record');
	}
}
