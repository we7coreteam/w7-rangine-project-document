<?php
/**
 * @author donknap
 * @date 18-8-3 上午11:02
 */

namespace W7\Core\Database;

use W7\App;

class DatabaseManager extends \Illuminate\Database\DatabaseManager {
	public function connection($name = null) {
		list($database, $type) = $this->parseConnectionName($name);
		$name = $name ?: $database;

		//这里不同于父函数，要做一个单例返回
		//外部还会接连接池，所以此处直接生成对象
		$connection = App::getApp()->getContext()->getContextDataByKey('db-transaction');
		if ($connection) {
			$this->connections[$name] = $connection;
		} else {
			$this->connections[$name] = $this->configure(
				$this->makeConnection($database),
				$type
			);
		}

		return $this->connections[$name];
	}
}
