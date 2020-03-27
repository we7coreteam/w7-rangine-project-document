<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class AlterStar extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('user_star', function (Blueprint $table) {
			$table->integer('chapter_id')->after('document_id')->nullable()->default(0);
			$table->dropUnique('user_id');
			$table->unique(['user_id', 'document_id', 'chapter_id'], 'user_document_chapter');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('user_star', function (Blueprint $table) {
			$table->dropUnique('user_document_chapter');
			$table->dropColumn('chapter_id');
			$table->unique(['user_id', 'document_id'], 'user_id');
		});
	}
}
