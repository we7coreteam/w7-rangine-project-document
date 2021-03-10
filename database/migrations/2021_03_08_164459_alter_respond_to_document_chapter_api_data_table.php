<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class AlterRespondToDocumentChapterApiDataTable2021_03_08_164459 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('document_chapter_api_data', function (Blueprint $table) {
			$table->longText('respond')->default('')->comment('响应数据')->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('document_chapter_api_data', function (Blueprint $table) {
			//
		});
	}
}
