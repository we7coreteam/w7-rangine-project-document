<?php
/**
 * @author donknap
 * @date 18-9-5 下午5:31
 */

namespace W7\Core\Database\Driver;

use Swoole\Coroutine\Mysql;

class MySqlCoroutine extends Mysql {
	public function lastInsertId() {
		return $this->insert_id;
	}
}