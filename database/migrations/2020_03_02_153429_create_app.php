<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateApp extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->create('app', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', '30');
			$table->string('appid', '18');
			$table->string('appsecret', '64');
			$table->integer('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		 $this->schema->dropIfExists('app');
	}
}
