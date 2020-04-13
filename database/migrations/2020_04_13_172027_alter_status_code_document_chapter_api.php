<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class AlterStatusCodeDocumentChapterApi extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('document_chapter_api', function (Blueprint $table) {
			$table->integer('status_code')->comment('状态码')->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('document_chapter_api', function (Blueprint $table) {
			$this->schema->table('document_chapter_api', function (Blueprint $table) {
				$table->dropColumn('status_code');
			});
		});
	}
}
