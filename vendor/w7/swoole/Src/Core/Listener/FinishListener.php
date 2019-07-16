<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:18
 */

namespace W7\Core\Listener;

use W7\Core\Config\Event;
use W7\Core\Message\MessageAbstract;
use W7\Core\Message\TaskMessage;

/**
 * onFinish(\Swoole\Server $serv, int $task_id, string $data)
 */
class FinishListener implements ListenerInterface {
	public function run(...$params) {
		/**
		 * @var TaskMessage $taskMessage
		 */
		list($server, $task_id, $taskMessage) = $params;

		if (!($taskMessage instanceof MessageAbstract)) {
			throw new \RuntimeException($taskMessage);
		}

		//echo '这里是回调函数' . $task_id . PHP_EOL;
		//处理在消息中设置的回调方法，如果未指定，则看任务中是否包含 finish 函数，否则什么不执行
		$callback = $taskMessage->getFinishCallback();
		if (!empty($callback)) {
			call_user_func_array($callback, [$server, $task_id, $taskMessage->result, $taskMessage->params]);
		}

		if ($taskMessage->hasFinishCallback) {
			$task = iloader()->singleton($taskMessage->task);
			call_user_func_array([$task, 'finish'], [$server, $task_id, $taskMessage->result, $taskMessage->params]);
		}
		ievent(Event::ON_USER_TASK_FINISH, [$taskMessage->result]);
	}
}
