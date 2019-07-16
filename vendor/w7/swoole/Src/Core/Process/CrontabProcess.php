<?php
/**
 * @author donknap
 * @date 18-11-22 下午8:26
 */

namespace W7\Core\Process;

use Swoole\Process;
use W7\App;
use W7\Core\Crontab\CronMap;
use W7\Core\Dispatcher\ProcessDispatcher;
use W7\Core\Message\CrontabMessage;
use W7\Core\Message\Message;
use W7\Core\Message\TaskMessage;

class CrontabProcess extends ProcessAbstract {
	private $setting;
	/**
	 * @var CronMap
	 */
	private $cronMap;
	const NAMEKEY = 'crontab';

	public function check() {
		$this->setting = \iconfig()->getUserAppConfig('crontab');
		$this->registerTasks();

		if (isset($this->setting['enabled']) && empty($this->setting['enabled'])) {
			return false;
		}
		if ($this->cronMap->count() !== 0) {
		    return true;
		} else {
			return false;
		}
	}

	private function registerTasks() {
		$this->cronMap = new CronMap(\iconfig()->getUserConfig(self::NAMEKEY));
	}

	public function run(Process $process) {
		//最小细度为一分钟
		swoole_timer_tick(1000, function () {
			if ((ENV & DEBUG) === DEBUG) {
				echo 'Crontab run at ' . date('Y-m-d H:i:s') . PHP_EOL;
			}

			$tasks = $this->cronMap->getRunTasks();
			foreach ($tasks as $name => $task) {
				ilogger()->info('Crontab task ' . $name . ' ' . $task);
				$this->runTask($name, $task);
			}
		});
	}

	public function read(Process $process, $data) {
		$message = Message::unpack($data);
		$this->cronMap->finishTask($message->name);
		return true;
	}

	/**
	 * 任务执行完成后，标记状态
	 * 因为此函数是在 onFinish 事件中调用到，此事件和当前进程不在同一个进程内
	 * 所以需要需要通道发送数据到此进程的 read 函数中处理
	 * 此方法逻辑上不应该在这里，但是为了方便代码维护，放到这里
	 */
	public function finishTask($server, $taskId, $result, $params) {
		ilogger()->info($params['cronTask'] . ' finished');
		/**
		 * @var ProcessDispatcher $processManager
		 */
		$message = new CrontabMessage();
		$message->name = $params['cronTask'];
		iprocessManager()->get(CrontabProcess::class)->write($message->pack());
	}

	/**
	 * 自定义进程中没办法调用 $server->task() 方法来发起任务
	 * 此处通过 $server->sendMessage() 将消息发送到 work 进程
	 * 再由 work 进程侦听 pipeMessage 消息来发起任务
	 * 发送任务时会将执行的任务标记为1，执行完调用 OnFinish 回调时，再变更状态
	 */
	private function runTask($name, $task) {
		$taskMessage = new TaskMessage();
		$taskMessage->type = TaskMessage::OPERATION_TASK_ASYNC;
		$taskMessage->task = $task;
		$taskMessage->params['cronTask'] = $name;
		$taskMessage->setFinishCallback(static::class, 'finishTask');

		if (App::$server->getServer()->sendMessage($taskMessage->pack(), 0)) {
			$this->cronMap->runTask($name);
		}
	}
}