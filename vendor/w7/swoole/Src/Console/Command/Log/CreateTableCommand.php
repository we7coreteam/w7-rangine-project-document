<?php

namespace W7\Console\Command\Log;

use Illuminate\Database\Schema\Blueprint;
use W7\Console\Command\Db\TableCommandAbstract;

class CreateTableCommand extends TableCommandAbstract {
	protected $table = 'log';
	protected $operate = 'create';

	protected function tableStruct(Blueprint $table) {
		$table->increments('id');
		$table->string('channel', 30);
		$table->integer('level');
		$table->text('message');
		$table->addColumn('integer','created_at', ['length' => 11]);
	}
}