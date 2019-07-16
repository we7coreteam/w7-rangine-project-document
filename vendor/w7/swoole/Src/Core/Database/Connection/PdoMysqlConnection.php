<?php
/**
 * @author donknap
 * @date 18-10-26 上午11:17
 */

namespace W7\Core\Database\Connection;


use Illuminate\Database\MySqlConnection;

class PdoMysqlConnection extends MySqlConnection {
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
		if ($this->pdo instanceof \PDO) {
			return $this->pdo;
		} else {
			return $this->readPdo;
		}
	}
}