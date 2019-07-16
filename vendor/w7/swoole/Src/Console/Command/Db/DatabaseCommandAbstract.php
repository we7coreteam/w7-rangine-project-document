<?php

namespace W7\Console\Command\Db;

use Illuminate\Database\Schema\MySqlBuilder;
use Symfony\Component\Console\Input\InputOption;
use W7\Console\Command\CommandAbstract;

abstract class DatabaseCommandAbstract extends CommandAbstract {
	protected $operate;
	/**
	 * @var MySqlBuilder
	 */
	protected $schema;


	protected function configure() {
		$this->addOption('connection', '-c', InputOption::VALUE_OPTIONAL, 'database channel', 'default');
	}

	protected function handle($options) {
		$options['connection'] = $options['connection'] ?? 'default';
		$this->schema = idb()->connection($options['connection'])->getSchemaBuilder();
		return $this->{$this->operate}($options);
	}
}