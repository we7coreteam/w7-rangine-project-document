<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateDocumentSearchHotTable2021_03_29_110505 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->create('document_search_hot', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('search_word', 255)->default('')->comment('搜索词');
			$table->integer('created_at')->nullable()->default(0);
			$table->integer('updated_at')->nullable()->default(0);
			$table->index('search_word');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->dropIfExists('document_search_hot');
	}
}
