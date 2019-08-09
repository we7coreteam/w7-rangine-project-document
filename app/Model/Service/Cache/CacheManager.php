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

namespace W7\App\Model\Service\Cache;

class CacheManager
{
	public static $store;
	protected $stores = [];

	public static function getStore($name = null)
	{
		if(!self::$store){
			$self = new self();
			self::$store = $self->store($name);
		}
		return self::$store;
	}

	public function store($name = null)
	{
		$name = $name ?: $this->getDefaultDriver();

		return $this->stores[$name] = $this->get($name);
	}

	protected function get($name)
	{
		return $this->stores[$name] ?? $this->resolve($name);
	}

	protected function resolve($name)
	{
		$driverMethod = 'create'.ucfirst($name).'Driver';
		if (method_exists($this, $driverMethod)) {
			return $this->{$driverMethod}();
		} else {
			throw new \Exception("Driver $name is not supported.");
		}
	}

	//数据库缓存
	protected function createDatabaseDriver()
	{
		return $this->repository(new DatabaseStore());
	}

	//redis缓存
	protected function createRedisDriver()
	{
		return $this->repository(new RedisStore());
	}

	public function repository(Store $store)
	{
		$repository = new Repository($store);
		return $repository;
	}

	public function getDefaultDriver()
	{
		return iconfig()->getUserAppConfig('cache_driver');
	}
}
