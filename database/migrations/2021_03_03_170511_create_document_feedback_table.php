<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateDocumentFeedbackTable2021_03_03_170511 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->create('document_feedback', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id')->nullable()->default(0);
			$table->integer('document_id')->nullable()->default(0);
			$table->tinyInteger('status')->nullable()->default(0)->comment('状态:0：未查看 1：已查看');
			$table->string('type', '200');
			$table->string('content','300');
			$table->string('images', '200');
			$table->integer('created_at')->nullable()->default(0);
			$table->integer('updated_at')->nullable()->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->dropIfExists('document_feedback');
	}
}
