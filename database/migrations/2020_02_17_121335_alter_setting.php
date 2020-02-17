<?php

use Illuminate\Database\Schema\Blueprint;
use W7\Core\Database\Migrate\Migration;

class AlterSetting extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->table('setting', function (Blueprint $table) {
			$table->text('value')->after('key');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table('setting', function (Blueprint $table) {
			$table->string('value', 1000)->after('key');
		});
	}
}
