<?php
/**
 * @author donknap
 * @date 18-11-24 下午10:03
 */

namespace W7\Core\Listener;


use Swoole\Http\Server;
use W7\Core\Message\Message;

class PipeMessageListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var Server $server
		 */
		list($server, $workId, $data) = $params;

		//管道不一定只能发送任务消息，需要先判断一下
		$message = Message::unpack($data);

		if ($message->messageType == Message::MESSAGE_TYPE_TASK) {
			if ($message->isTaskAsync()) {
				itask($message->task, $message->params);
			}
		}
	}
}