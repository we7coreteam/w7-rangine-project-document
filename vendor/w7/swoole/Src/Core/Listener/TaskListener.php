<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:18
 */

namespace W7\Core\Listener;

use Swoole\Http\Server;
use W7\Core\Dispatcher\TaskDispatcher;

class TaskListener implements ListenerInterface {
	public function run(...$params) {
		list($server, $taskId, $workId, $data) = $params;
		return $this->dispatchTask($server, $taskId, $workId, $data);
	}

	private function dispatchTask(Server $server, $taskId, $workId, $data) {
		/**
		 * @var TaskDispatcher $taskDispatcher
		 */
		$taskDispatcher = iloader()->singleton(TaskDispatcher::class);
		try {
			$result = $taskDispatcher->dispatch($server, $taskId, $workId, $data);
		} catch (\Exception $exception) {
			$server->finish($exception->getMessage());
			return;
		}
		if (empty($result)) {
			$result = true;
		}
		$server->finish($result);
	}
}
