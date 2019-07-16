<?php
/**
 * @author donknap
 * @date 18-12-30 上午11:49
 */

namespace W7\Core\Cache\Pool;


use W7\Core\Pool\CoPoolAbstract;

class Pool extends CoPoolAbstract {

	private $creator;

	public function setCreator($creator) {
		$this->creator = $creator;
	}

	public function createConnection() {
		if (empty($this->creator)) {
			throw new \RuntimeException('Invalid cache creator');
		}
		$connection = $this->creator->connect($this->config);
		$connection->poolName = sprintf('%s:%s', $this->config['driver'], $this->poolName);
		return $connection;
	}

	public function getConnection() {
		$connect = parent::getConnection();
		try {
			$result = $connect->ping();
			return $connect;
		} catch (\Throwable $e) {
			return $this->createConnection();
		}
	}
}