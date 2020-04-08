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

class AlterDocumentChapterApi extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->table('document_chapter_api', function (Blueprint $table) {
			$table->tinyInteger('body_param_location')->after('description')->default(3)->comment('body_param默认请求方式');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->table('document_chapter_api', function (Blueprint $table) {
			$table->dropColumn('body_param_location');
		});
	}
}
