<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateDocumentChapterApiReponseTable2020_05_27_092518 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create('document_chapter_api_reponse', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('chapter_id')->default(0)->comment('章节ID');
			$table->string('description', 255)->default('')->comment('响应描述');
			$table->index('chapter_id');
		});
		$this->schema->table('document_chapter_api_param', function (Blueprint $table) {
			$table->integer('reponse_id')->after('location')->default(0)->comment('响应数据ID');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists('document_chapter_api_reponse');
		$this->schema->table('document_chapter_api_param', function (Blueprint $table) {
			$table->dropColumn('reponse_id');
		});
	}
}
