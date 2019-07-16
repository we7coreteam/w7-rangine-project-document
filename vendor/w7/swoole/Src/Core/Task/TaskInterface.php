<?php
/**
 * @author donknap
 * @date 18-7-25 下午3:04
 */

namespace W7\Core\Task;

use Swoole\Server;

interface TaskInterface {
	/**
	 * 线程具体执行内容
	 * @return mixed
	 */
	public function run(Server $server, $taskId, $workId, $data);

	/**
	 * 任务中定义完成回调
	 */
	public function finish(Server $server, $taskId, $data, $params);
}