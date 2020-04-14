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

class CreateDocumentChapterApi extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create('document_chapter_api', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('chapter_id')->default(0)->comment('章节ID');
			$table->string('url', 255)->default('')->comment('接口地址');
			$table->tinyInteger('method')->comment('请求方法:1、GET,2、POST,3、PUT,4、OPTIONS,5、DELETE');
			$table->integer('status_code')->comment('状态码');
			$table->string('description', 255)->default('')->comment('接口描述');
			$table->tinyInteger('body_param_location')->default(3)->comment('body_param默认请求方式');
			$table->index('chapter_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->dropIfExists('document_chapter_api');
	}
}
