<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateUserThirdParty extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->create('user_third_party', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('uid');
			$table->string('openid', '200');
			$table->string('username', '100');
			$table->smallInteger('source');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		 $this->schema->dropIfExists('user_third_party');
	}
}
