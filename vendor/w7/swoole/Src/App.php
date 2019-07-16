<?php
/**
 * @author donknap
 * @date 18-7-19 上午10:25
 */

namespace W7;

use W7\Console\Application;
use W7\Core\Cache\Cache;
use W7\Core\Config\Config;
use W7\Core\Helper\Loader;
use W7\Core\Log\Logger;
use W7\Core\Log\LogManager;
use W7\Core\Helper\Storage\Context;
use W7\Core\Provider\ProviderManager;
use W7\Http\Server\Server;

class App {
	private static $self;

	/**
	 * 服务器对象
	 *
	 * @var Server
	 */
	public static $server;
	/**
	 * @var Loader
	 */
	private $loader;

	public static function getApp() {
		if (!empty(self::$self)) {
			return self::$self;
		}
		self::$self = new static();

		return self::$self;
	}

	public function runConsole() {
		try{
			//初始化配置
			iconfig();
			iloader()->singleton(ProviderManager::class)->register()->boot();
			$console = iloader()->singleton(Application::class);

			$console->run();
		} catch (\Throwable $e) {
			ioutputer()->error($e->getMessage());
		}
	}

	public function getLoader() {
		if (empty($this->loader)) {
			$this->loader = new Loader();
		}
		return $this->loader;
	}

	/**
	 * @return Logger
	 */
	public function getLogger() {
		/**
		 * @var LogManager $logManager
		 */
		$logManager = iloader()->singleton(LogManager::class);
		return $logManager->getDefaultChannel();
	}

	/**
	 * @return Context
	 */
	public function getContext() {
		return $this->getLoader()->singleton(Context::class);
	}

	public function getConfigger() {
		return $this->getLoader()->singleton(Config::class);
	}

	/**
	 * @return Cache
	 */
	public function getCacher() {
		/**
		 * @var Cache $cache;
		 */
		$cache = $this->getLoader()->singleton(Cache::class);
		return $cache;
	}
}
