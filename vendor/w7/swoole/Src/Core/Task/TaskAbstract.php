<?php
/**
 * @author donknap
 * @date 18-11-12 下午4:29
 */

namespace W7\Core\Task;


use Swoole\Server;

abstract class TaskAbstract implements TaskInterface {
	public function finish(Server $server, $taskId, $data, $params) {

	}
}