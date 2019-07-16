<?php
/**
 * @author donknap
 * @date 18-7-19 上午10:49
 */

use Swoole\Coroutine;
use Symfony\Component\VarDumper\VarDumper;
use W7\App;
use W7\Core\Dispatcher\EventDispatcher;
use W7\Core\Dispatcher\TaskDispatcher;
use W7\Core\Exception\DumpException;

if (!function_exists('iprocess')) {
	/**
	 * 派发一个进程
	 * @param $name
	 * @param null $server
	 * @return bool|\Swoole\Process|void
	 */
	function iprocess($name, $server = null) {
		/**
		 * @var \W7\Core\Dispatcher\ProcessDispatcher $dispatcher
		 */
		$dispatcher = iloader()->singleton(\W7\Core\Dispatcher\ProcessDispatcher::class);
		return $dispatcher->dispatch($name, $server);
	}

	/**
	 * 返回进程管理器
	 * @return \W7\Core\Dispatcher\ProcessDispatcher
	 */
	function iprocessManager() {
		$dispatcher = iloader()->singleton(\W7\Core\Dispatcher\ProcessDispatcher::class);
		return $dispatcher;
	}

	/**
	 * 派发一个进程池
	 * @param $name
	 * @param null $server
	 * @return bool|\Swoole\Process|void
	 */
	function iprocessPool($name, $number) {

	}

	/**
	 * 返回一个进程池管理
	 * @return \W7\Core\Dispatcher\ProcessPoolDispatcher
	 */
	function iprocessPoolManager() {
		$dispatcher = iloader()->singleton(\W7\Core\Dispatcher\ProcessPoolDispatcher::class);
		return $dispatcher;
	}
}
if (!function_exists("ievent")) {
	/**
	 * 派发一个事件
	 * @param $eventName
	 * @param array $args
	 * @return bool
	 * @throws Exception
	 */
	function ievent($eventName, $args = [])
	{
		/**
		 * @var EventDispatcher $dispatcher
		 */
		$dispatcher = iloader()->singleton(EventDispatcher::class);
		return $dispatcher->dispatch($eventName, $args);
	}
}
if (!function_exists("itask")) {
	/**
	 * 派发一个异步任务
	 * @param string $taskName
	 * @param array $params
	 * @param int $timeout
	 * @return false|int
	 * @throws \W7\Core\Exception\TaskException
	 */
	function itask($taskName, $params = [], int $timeout = 3) {
		//构造一个任务消息
		$taskMessage = new \W7\Core\Message\TaskMessage();
		$taskMessage->task = $taskName;
		$taskMessage->params = $params;
		$taskMessage->timeout = $timeout;
		$taskMessage->type = \W7\Core\Message\TaskMessage::OPERATION_TASK_ASYNC;
		/**
		 * @var TaskDispatcher $dispatcherMaker
		 */
		$dispatcherMaker = iloader()->singleton(TaskDispatcher::class);
		return $dispatcherMaker->register($taskMessage);
	}

	function itaskCo($taskName, $params = [], int $timeout = 3) {
		//构造一个任务消息
		$taskMessage = new \W7\Core\Message\TaskMessage();
		$taskMessage->task = $taskName;
		$taskMessage->params = $params;
		$taskMessage->timeout = $timeout;
		$taskMessage->type = \W7\Core\Message\TaskMessage::OPERATION_TASK_CO;
		/**
		 * @var TaskDispatcher $dispatcherMaker
		 */
		$dispatcherMaker = iloader()->singleton(TaskDispatcher::class);
		return $dispatcherMaker->registerCo($taskMessage);
	}
}

if (!function_exists("iuuid")) {
	/**
	 * 获取UUID
	 * @return string
	 */
	function iuuid() {
		$len = rand(2, 16);
		$prefix = md5(substr(md5(Coroutine::getuid()), $len));
		return uniqid($prefix);
	}
}

if (!function_exists('iloader')) {
	/**
	 * 获取加载器
	 * @return \W7\Core\Helper\Loader
	 */
	function iloader() {
		return \W7\App::getApp()->getLoader();
	}
}

if (!function_exists('ioutputer')) {
	/**
	 * 获取输出对象
	 * @return W7\Console\Io\Output
	 */
	function ioutputer() {
		return iloader()->singleton(\W7\Console\Io\Output::class);
	}
}

if (!function_exists('iinputer')) {
	/**
	 * 输入对象
	 * @return W7\Console\Io\Input
	 */
	function iinputer() {
		return iloader()->singleton(\W7\Console\Io\Input::class);
	}
}

if (!function_exists('iconfig')) {
	/**
	 * 输入对象
	 * @return W7\Core\Config\Config
	 */
	function iconfig() {
		return App::getApp()->getConfigger();
	}
}

if (!function_exists("ilogger")) {
	/**
	 * 返回logger对象
	 * @return \W7\Core\Log\Logger
	 */
	function ilogger() {
		return App::getApp()->getLogger();
	}
}

if (!function_exists("idb")) {
	/**
	 * 返回一个数据库连接对象
	 * @return \W7\Core\Database\DatabaseManager
	 */
	function idb() {
		return \Illuminate\Database\Eloquent\Model::getConnectionResolver();
	}
}

if (!function_exists("icontext")) {
	/**
	 * 返回logger对象
	 * @return \W7\Core\Helper\Storage\Context
	 */
	function icontext() {
		return App::getApp()->getContext();
	}
}

if (!function_exists("icache")) {
	/**
	 * @return \W7\Core\Cache\Cache
	 */
	function icache() {
		return App::getApp()->getCacher();
	}
}

if (!function_exists("irouter")) {
	/**
	 * @return \W7\Core\Route\Route
	 */
	function irouter() {
		return iloader()->singleton(\W7\Core\Route\Route::class);
	}
}

if (!function_exists('isCo')) {
	/**
	 * 是否是在协成
	 * @return bool
	 */
	function isCo():bool {
		return Coroutine::getuid() > 0;
	}
}

if (!function_exists("getClientIp")) {
	function getClientIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return $_SERVER['REMOTE_ADDR'];
	}
}

if (!function_exists("isWorkerStatus")) {
	function isWorkerStatus() {
		if (App::$server === null) {
			return false;
		}

		$server = App::$server->getServer();
		if ($server->manager_pid == 0) {
			return false;
		}
		if ($server && \property_exists($server, 'taskworker') && ($server->taskworker === false)) {
			return true;
		}

		return false;
	}
}

if (!function_exists('isetProcessTitle')) {
	function isetProcessTitle($title) {
		if (\stripos(PHP_OS, 'Darwin') !== false) {
			return true;
		}
		if (\function_exists('cli_set_process_title')) {
			return cli_set_process_title($title);
		}

		if (\function_exists('swoole_set_process_name')) {
			return swoole_set_process_name($title);
		}
		return true;
	}
}

if (!function_exists('irandom')) {
	function irandom($length, $numeric = FALSE) {
		$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
		if ($numeric) {
			$hash = '';
		} else {
			$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
			$length--;
		}
		$max = strlen($seed) - 1;
		for ($i = 0; $i < $length; $i++) {
			$hash .= $seed{mt_rand(0, $max)};
		}
		return $hash;
	}
}

if (!function_exists('idd')) {
	function idd(...$vars) {
		ob_start();
		$_SERVER['VAR_DUMPER_FORMAT'] = 'html';
		foreach ($vars as $var) {
			VarDumper::dump($var);
		}
		VarDumper::setHandler(null);
		$content = ob_get_clean();

		throw new DumpException($content);
	}
}

if (!function_exists('ienv')) {
	function ienv($key, $default = null) {
		$value = getenv($key);

		if ($value === false) {
			return value($default);
		}

		//常量解析

		if (strpos($value, '|') !== false || strpos($value, '^') !== false) {
			$exec = 'return ' . $value . ';';
			try{
				$value = eval($exec);
			} catch (Throwable $e) {
				//
			}
		} else if (defined($value)) {
			$value = constant($value);
		}

		switch (strtolower($value)) {
			case 'true':
			case '(true)':
				return true;
			case 'false':
			case '(false)':
				return false;
			case 'empty':
			case '(empty)':
				return '';
			case 'null':
			case '(null)':
				return;
		}

		if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
			return substr($value, 1, -1);
		}

		return $value;
	}
}