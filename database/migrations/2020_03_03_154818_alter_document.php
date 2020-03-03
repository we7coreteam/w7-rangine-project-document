<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class AlterDocument extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('document', function (Blueprint $table) {
			$table->string('cover', 120)->after('creator_id')->nullable()->default('');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('document', function (Blueprint $table) {
			$table->dropColumn('cover');
		});
	}
}
