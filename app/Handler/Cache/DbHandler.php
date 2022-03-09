<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Handler\Cache;

use W7\App\Model\Service\DbCacheLogic;
use W7\Core\Cache\Handler\HandlerAbstract;

class DbHandler extends HandlerAbstract
{
	protected $storage;

	public static function connect($config): HandlerAbstract
	{
		$cacheDb = (new DbCacheLogic());
		return new static($cacheDb);
	}

	public function set($key, $value, $ttl = null)
	{
		return $this->storage->set($key, $value, $ttl);
	}

	public function get($key, $default = null)
	{
		return $this->storage->get($key);
	}

	public function has($key)
	{
		return $this->storage->has($key);
	}

	public function setMultiple($values, $ttl = null)
	{
		return $this->storage->setMultiple($values);
	}

	public function getMultiple($keys, $default = null)
	{
		return $this->storage->getMultiple($keys);
	}

	public function delete($key)
	{
		return $this->storage->delete($key);
	}

	public function deleteMultiple($keys)
	{
		return $this->storage->deleteMultiple($keys);
	}

	public function clear()
	{
		return $this->storage->clear();
	}
}
