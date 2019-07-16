<?php
/**
 * @author donknap
 * @date 18-11-24 下午9:54
 */

namespace W7\Core\Message;


/**
 * 投递一个任务时的消息结构
 */
class TaskMessage extends MessageAbstract {
	use MessageTraiter;

	public $messageType = Message::MESSAGE_TYPE_TASK;
	/**
	 * 发起异步任务
	 */
	const OPERATION_TASK_ASYNC = '1';
	/**
	 * 发起协程任务
	 */
	const OPERATION_TASK_CO = '2';

	/**
	 * 发起的任务类型
	 */
	public $type;

	/**
	 * @var mixed 发起任务操作时，此为任务名
	 */
	public $task = '';

	/**
	 * 任务超时，仅当协程，异步阻塞任务时有用
	 * @var int
	 */
	public $timeout = 3;

	/**
	 * 一些附加参数
	 * @var array
	 */
	public $params = [];

	/**
	 * 派发任务时，指定任务中的默认方法
	 */
	public $method = 'run';

	/**
	 * 保存任务执行结果，
	 * 因为需要将消息继续传递给onFinish事件
	 * 在onFinish事件中，需要处理回调和其它工作
	 * @var array
	 */
	public $result = [];

	/**
	 * 是否包含回调finish函数
	 * @var bool
	 */
	public $hasFinishCallback = false;

	public function isTaskAsync() {
		if ($this->type == self::OPERATION_TASK_ASYNC) {
			return true;
		} else {
			return false;
		}
	}

	public function isTaskCo() {
		if ($this->type == self::OPERATION_TASK_CO) {
			return true;
		} else {
			return false;
		}
	}

	public function setFinishCallback($class, $method) {
		$this->params['finish'] =  [$class, $method];
	}

	public function getFinishCallback() {
		$callback = $this->params['finish'] ?? null;
		if (empty($callback)) {
			return false;
		}

		if (!class_exists($callback[0])) {
			return false;
		}

		$object = iloader()->singleton($callback[0]);
		if (!method_exists($object, $callback[1])) {
			return false;
		}

		return [$object, $callback[1]];
	}
}