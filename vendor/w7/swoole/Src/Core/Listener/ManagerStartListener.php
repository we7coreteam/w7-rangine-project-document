<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:18
 */

namespace W7\Core\Listener;

use W7\App;

class ManagerStartListener extends ListenerAbstract {
	public function run(...$params) {
		$this->setServerTitle($params[0]);
	}

	private function setServerTitle($server) {
		\isetProcessTitle( 'w7swoole ' . App::$server->type . ' manager process');
	}
}
