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
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('user_star', function (Blueprint $table) {
			$table->dropColumn('chapter_id');
		});
	}
}
