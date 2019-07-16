<?php
/**
 * @author donknap
 * @date 18-7-25 下午3:03
 */

namespace W7\Core\Process;

use Swoole\Process;
use W7\App;

class ReloadProcess implements ProcessInterface {

	/**
	 * 监听文件变化的路径
	 *
	 * @var string
	 */
	private $watchDir = [
		APP_PATH,
		BASE_PATH. DIRECTORY_SEPARATOR. 'config'
	];

	/**
	 * the lasted md5 of dir
	 *
	 * @var string
	 */
	private $md5File = '';

	/**
	 * the interval of scan
	 *
	 * @var int
	 */
	private $interval = 5;

	private $enabled = false;

	private $debug = false;

	/**
	 * 初始化方法
	 */
	public function __construct() {
		$reloadConfig = \iconfig()->getUserAppConfig('reload');
		$this->interval = !empty($reloadConfig['interval']) ? $reloadConfig['interval'] : $this->interval;
		$this->enabled = ((ENV & DEBUG) === DEBUG);
		$this->debug = (bool)$reloadConfig['debug'];
		$this->watchDir = array_merge($this->watchDir, $reloadConfig['path'] ?? []);

		$this->md5File = $this->getWatchDirMd5();
	}

	public function check() {
		if ($this->enabled) {
			return true;
		}
		return false;
	}

	public function run(Process $process) {
		$server = App::$server;
		if ($this->debug) {
			ioutputer()->writeln("Start automatic reloading every {$this->interval} seconds ...");
		}
		while (true) {
			sleep($this->interval);
			if ($this->debug) {
				$startReload = true;
			} else {
				$md5File = $this->getWatchDirMd5();
				$startReload = (strcmp($this->md5File, $md5File) !== 0);
				$this->md5File = $md5File;
			}
			if ($startReload) {
				$server->isRun();
				$server->getServer()->reload();
				if (ini_get('opcache.enable') || ini_get('opcache.enable_cli')) {
					opcache_reset();
				}
				if (!$this->debug) {
					ioutputer()->writeln("Reloaded in " . date('m-d H:i:s') . "...");
				}
			}
		}
	}

	/**
	 * md5 of dir
	 *
	 * @param string $dir
	 *
	 * @return bool|string
	 */
	private function md5File($dir) {
		if (!is_dir($dir)) {
			return '';
		}

		$md5File = array();
		$d	   = dir($dir);
		while (false !== ($entry = $d->read())) {
			if ($entry !== '.' && $entry !== '..') {
				if (is_dir($dir . '/' . $entry)) {
					$md5File[] = $this->md5File($dir . '/' . $entry);
				} elseif (substr($entry, -4) === '.php') {
					$md5File[] = md5_file($dir . '/' . $entry);
				}
				$md5File[] = $entry;
			}
		}
		$d->close();

		return md5(implode('', $md5File));
	}

	private function getWatchDirMd5() {
		$md5 = [];
		foreach ($this->watchDir as $dir) {
			$md5[] = $this->md5File($dir);
		}
		return md5(implode('', $md5));
	}
}
