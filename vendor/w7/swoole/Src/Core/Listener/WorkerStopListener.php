<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:18
 */

namespace W7\Core\Listener;

class WorkerStopListener implements ListenerInterface {
	public function run(...$params) {
		ilogger()->flushLog();
	}
}
