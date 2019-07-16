<?php
/**
 * @author donknap
 * @date 18-8-1 下午5:44
 */

namespace W7\Core\Database\Connection;

use Illuminate\Database\MySqlConnection;
use W7\Core\Database\Driver\MySqlCoroutine;

class SwooleMySqlConnection extends MySqlConnection {
	public function select($query, $bindings = [], $useReadPdo = true) {
		return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
			if ($this->pretending()) {
				return [];
			}
			$statement = $this->getPdoForSelect($useReadPdo)->prepare($query);
			if (!empty($this->getPdoForSelect()->error)) {
				throw new \RuntimeException($this->getPdo()->error);
			}
			$statement->execute($this->prepareBindings($bindings));
			return $statement->fetchAll();
		});
	}

	public function statement($query, $bindings = []) {
		return $this->run($query, $bindings, function ($query, $bindings) {
			if ($this->pretending()) {
				return true;
			}
			$statement = $this->getPdo()->prepare($query);
			if (!empty($this->getPdo()->error)) {
				throw new \RuntimeException($this->getPdo()->error);
			}
			$this->recordsHaveBeenModified();
			return $statement->execute($this->prepareBindings($bindings));
		});
	}

	public function affectingStatement($query, $bindings = []) {
		return $this->run($query, $bindings, function ($query, $bindings) {
			if ($this->pretending()) {
				return 0;
			}
			$statement = $this->getPdo()->prepare($query);
			$statement->execute($this->prepareBindings($bindings));
			if (!empty($this->getPdo()->error)) {
				throw new \RuntimeException($this->getPdo()->error);
			}
			$this->recordsHaveBeenModified(
				($count = $statement->affected_rows) > 0
			);
			return $count;
		});
	}

	public function getPoolName() {
		$activeConnection = $this->getActiveConnection();
		if (!empty($activeConnection->poolName)) {
			return $activeConnection->poolName;
		}
		return '';
	}

	/**
	 * 获取当前活动的查询连接
	 */
	public function getActiveConnection() {
		if ($this->pdo instanceof MySqlCoroutine) {
			return $this->pdo;
		} else {
			return $this->readPdo;
		}
	}
}
