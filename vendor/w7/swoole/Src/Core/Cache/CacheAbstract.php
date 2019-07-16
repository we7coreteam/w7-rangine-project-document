<?php
/**
 * @author donknap
 * @date 18-12-30 下午5:38
 */

namespace W7\Core\Cache;


use Psr\SimpleCache\CacheInterface;

abstract class CacheAbstract implements CacheInterface {

	/**
	 * @var ConnectorManager
	 */
	protected $manager;
	protected $channelName = 'default';

	/**
	 * 选择一个缓存通道
	 * @param $name
	 * @return $this
	 */
	public function channel($name) {
		if (empty($name)) {
			throw new \RuntimeException('Invalid cache channel name');
		}
		$cacher = iloader()->withClass(static::class)->withSingle()->withAlias($name)->get();
		$cacher->setChannelName($name);
		return $cacher;
	}

	protected function getConnection() {
		$this->manager = iloader()->singleton(ConnectorManager::class);
		return $this->manager->connect($this->channelName);
	}

	public function setChannelName(string $channelName) {
		$this->channelName = $channelName;
	}

	protected function unserialize($data) {
		return is_numeric($data) ? $data : unserialize($data);
	}

	protected function serialize($data) {
		return is_numeric($data) ? $data : serialize($data);
	}
}