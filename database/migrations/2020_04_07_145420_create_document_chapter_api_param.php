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

class CreateDocumentChapterApiParam extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->create('document_chapter_api_param', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('chapter_id')->default(0)->comment('章节ID');
			$table->integer('parent_id')->default(0)->comment('上级ID');
			$table->tinyInteger('location')->comment('请求类型：1request.header,2request.query3requeset.body.form-data4requeset.body.x-www-form-urlencoded5requeset.body.raw6requeset.body.binary 7reponse.header8、reponse.body.form9、requeset.body.x-www-form-urlencoded10、requeset.body.raw11、requeset.body.binary');
			$table->tinyInteger('type')->comment('数据类型:1、int2、string...');
			$table->string('name', 255)->default('')->comment('数据键值');
			$table->string('description', 255)->default('')->comment('数据键值描述');
			$table->tinyInteger('enabled')->default(1)->comment('是否必填1否2是');
			$table->string('default_value', 255)->default('')->comment('初始值');
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
		$this->schema->dropIfExists('document_chapter_api_param');
	}
}
