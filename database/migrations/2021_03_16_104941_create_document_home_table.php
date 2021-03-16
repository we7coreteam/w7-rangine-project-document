<?php

use Illuminate\Database\Schema\Blueprint;
use W7\DatabaseTool\Migrate\Migration;

class CreateDocumentHomeTable2021_03_16_104941 extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$this->schema->create('document_home', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->tinyInteger('type')->comment('首页类型:1、公告,2、首页类型一,3、首页类型二');
			$table->integer('sort')->default(0)->comment('排序值');
			$table->string('logo', 255)->default('')->comment('图标');
			$table->string('url', 255)->default('')->comment('访问地址');
			$table->integer('document_id')->default(0)->comment('文档id');
			$table->integer('user_id')->default(0)->comment('用户id');
			$table->string('description', 255)->default('')->comment('文档简介');
			$table->integer('created_at')->nullable()->default(0);
			$table->integer('updated_at')->nullable()->default(0);
			$table->index('document_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->dropIfExists('document_home');
	}
}
