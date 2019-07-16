<?php
/**
 * @author donknap
 * @date 18-7-25 下午2:49
 */

namespace W7\Core\Config;

class Event
{
	/**
	 * swoole 事件
	 */
	const ON_START = 'start';
	const ON_SHUTDOWN = 'shutdown';

	const ON_WORKER_START = 'workerStart';
	const ON_WORKER_STOP = 'workerStop';
	const ON_WORKER_EXIT = 'workerExit';
	const ON_WORKER_ERROR = 'workerError';

	const ON_MANAGER_START = 'managerStart';
	const ON_MANAGER_STOP = 'managerStop';

	const ON_CONNECT = 'connect';
	const ON_RECEIVE = 'receive';
	const ON_PACKET = 'packet';
	const ON_CLOSE = 'close';

	const ON_BUFFER_FULL = 'bufferFull';
	const ON_BUFFER_EMPTY = 'bufferEmpty';

	const ON_TASK = 'task';
	const ON_FINISH = 'finish';
	const ON_PIPE_MESSAGE = 'pipeMessage';

	const ON_REQUEST = 'request';

	/**
	 * 自定义事件
	 */

    const ON_USER_BEFORE_START = 'beforeStart';
    const ON_USER_AFTER_START = 'afterStart';
    const ON_USER_BEFORE_REQUEST = 'beforeRequest';
    const ON_USER_AFTER_REQUEST = 'afterRequest';
    const ON_USER_TASK_FINISH = 'afterTaskFinish';
}
