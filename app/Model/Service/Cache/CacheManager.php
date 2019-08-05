<?php
namespace W7\App\Model\Service\Cache;


use Illuminate\Support\Str;


class CacheManager
{
	protected $stores = [];


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
