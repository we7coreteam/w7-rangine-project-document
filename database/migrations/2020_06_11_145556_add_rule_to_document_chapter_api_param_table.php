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

class AddRuleToDocumentChapterApiParamTable2020_06_11_145556 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->table('document_chapter_api_param', function (Blueprint $table) {
			$table->string('rule', 255)->after('enabled')->default('')->comment('moke规则');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->table('document_chapter_api_param', function (Blueprint $table) {
			$table->dropColumn('rule');
		});
	}
}
