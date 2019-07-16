<?php

namespace W7\Core\Log\Driver;

use Monolog\Handler\AbstractProcessingHandler;

class MysqlHandler extends AbstractProcessingHandler {
	protected $table;
	protected $connection;

	static public function getHandler($config) {
		$handle = new static();
		$handle->table = $config['table'] ?? 'log';
		$handle->connection = $config['connection'] ?? 'default';
		return $handle;
	}

	public function handleBatch(array $records) {
		foreach ($records as &$record) {
			$record = [
				'channel' => $record['channel'],
				'level' => $record['level'],
				'message' => $record['message'],
				'created_at' => $record['datetime']->format('U')
			];
		}
		idb()->connection($this->connection)->table($this->table)->insert($records);
	}

	protected function write(array $record) {

	}
}