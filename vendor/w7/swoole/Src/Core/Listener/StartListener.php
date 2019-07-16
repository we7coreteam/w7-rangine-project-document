<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:18
 */

namespace W7\Core\Listener;

use W7\App;

class StartListener implements ListenerInterface {
	public function run(...$params) {
		\isetProcessTitle( 'w7swoole ' . App::$server->type . ' master process');
	}
}
