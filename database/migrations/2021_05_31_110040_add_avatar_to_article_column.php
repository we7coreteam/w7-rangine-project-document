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

class AddAvatarToArticleColumn2021_05_31_110040 extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->schema->table('article_column', function (Blueprint $table) {
			$table->string('avatar', 255)->default('')->after('name')->comment('栏目头像');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$this->schema->table('article_column', function (Blueprint $table) {
			$table->dropColumn('avatar');
		});
	}
}
