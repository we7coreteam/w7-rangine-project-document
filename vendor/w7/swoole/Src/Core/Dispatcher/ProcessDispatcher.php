<?php
/**
 * author: alex
 * date: 18-8-3 上午10:46
 */

namespace W7\Core\Dispatcher;

use Swoole\Process;
use W7\Core\Process\ProcessAbstract;

class ProcessDispatcher extends DispatcherAbstract {

	/**
	 * @var array
	 */
	private static $processes = [];

	public function register() {

	}

	public function dispatch(...$params) {
		$name = $params[0];
		$server = $params[1];

		if (!class_exists($name)) {
			ilogger()->warning(sprintf("Process is worng name is %s", $name));
			return false;
		}
		/**
		 * @var ProcessAbstract $process
		 */
		$process = new $name();
		$checkInfo = call_user_func([$process, "check"]);
		if (!$checkInfo) {
			return false;
		}

		/**
		 * @var Process $swooleProcess
		 */
		$swooleProcess = new Process(function (Process $worker) use ($process, $name) {
			if (\stripos(PHP_OS, 'Darwin') === false) {
				$worker->name('w7swoole ' . $name . '-' . $worker->pipe . ' process');
			}
			$process->run($worker);
			//如果进程包含read方法，自动添加事件侦听，获取主进程发送的消息
			if (method_exists($process, 'read')) {
				//增加事件循环，将消息接收到类中
				swoole_event_add($worker->pipe, function($pipe) use ($worker, $process) {
					$recv = $worker->read();
					if (!$process->read($worker, $recv)) {
						swoole_event_del($worker->pipe);
					}
					sleep($process->readInterval);
				});
			}

		}, false, SOCK_DGRAM);

		//可能相同的进程会注册多个
		if (!isset(self::$processes[$name]) || !is_array(self::$processes[$name])) {
			self::$processes[$name] = [];
		}
		array_push(self::$processes[$name], $swooleProcess);

		if (!empty($server)) {
			$server->addProcess($swooleProcess);
		} else {
			$swooleProcess->useQueue();
			$swooleProcess->start();
		}

		return $swooleProcess;
	}

	/**
	 * @param $name
	 * @param int $index
	 * @return Process
	 */
	public function get($name, $index = 0) {
		/**
		 * @var Process $process
		 */
		$process = self::$processes[$name] ?? null;
		if (empty($process) || empty($process[$index])) {
			throw new \RuntimeException('Process not exists');
		}

		return $process[$index];
	}

	public function quit($name) {
		try {
			foreach (self::$processes[$name] as $i => $process) {
				Process::kill($process->pid);
			}
			unset(self::$processes[$name]);
			return true;
		} catch (\Throwable $e) {
			return true;
		}
	}
}
