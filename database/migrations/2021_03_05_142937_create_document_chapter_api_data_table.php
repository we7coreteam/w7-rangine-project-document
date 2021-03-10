<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateDocumentChapterApiDataTable2021_03_05_142937 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->create('document_chapter_api_data', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('chapter_id')->default(0)->comment('章节ID');
			$table->string('respond', 500)->default('')->comment('响应数据');
			$table->index('chapter_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->dropIfExists('document_chapter_api_data');
	}
}
