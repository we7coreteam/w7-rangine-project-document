<?php
/**
 * @author donknap
 * @date 18-7-21 上午11:18
 */

namespace W7\Core\Listener;

use W7\App;

class WorkerStartListener implements ListenerInterface {
	public function run(...$params) {
		\isetProcessTitle( 'w7swoole ' . App::$server->type . (App::$server->server->taskworker ? ' task' : '')  . ' worker process');

		//设置安全限制目录
		$openBaseDirConfig = iconfig()->getUserAppConfig('setting')['basedir'] ?? [];
		if (is_array($openBaseDirConfig)) {
			$openBaseDirConfig = implode(':', $openBaseDirConfig);
		}

		$openBaseDir = [
			'/tmp',
			sys_get_temp_dir(),
			APP_PATH,
			RUNTIME_PATH,
			BASE_PATH . '/vendor',
			$openBaseDirConfig,
		];
		ini_set('open_basedir', implode(':', $openBaseDir));
	}
}
