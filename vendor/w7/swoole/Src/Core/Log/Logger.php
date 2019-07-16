<?php
/**
 * @author donknap
 * @date 18-10-18 下午7:31
 */

namespace W7\Core\Log;

class Logger extends \Monolog\Logger {
	/**
	 * @param $name
	 * @return \Monolog\Logger
	 */
	public function channel($name) {
		/**
		 * @var LogManager $logManager
		 */
		$logManager = iloader()->singleton(LogManager::class);
		return $logManager->getChannel($name);
	}

	public function addRecord($level, $message, array $context = array()) {
		$result =  parent::addRecord($level, $message, $context);

		if ($this->bufferLimit == 1) {
			$this->flushLog($this->getName());
		}
		return $result;
	}

	public function flushLog($channel = null) {
		$logManager = iloader()->singleton(LogManager::class);
		$loggers = $logManager->getLoggers($channel);

		foreach ($loggers as $logger) {
			foreach ($logger->getHandlers() as $handle) {
				$handle->flush();
			}
		}
	}

	public function __destruct() {
		$this->flushLog($this->name);
	}
}