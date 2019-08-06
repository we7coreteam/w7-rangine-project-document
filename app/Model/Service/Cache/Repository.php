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

use Closure;
use Illuminate\Cache\RetrievesMultipleKeys;

use Illuminate\Support\InteractsWithTime;

class Repository
{
	use InteractsWithTime, RetrievesMultipleKeys;

	protected $store;
	protected $default = 3600;

	public function __construct(Store $store)
	{
		$this->store = $store;
	}

	public function has($key)
	{
		return ! is_null($this->get($key));
	}

	public function get($key, $default = null)
	{
		if (is_array($key)) {
			return $this->getMany($key);
		}
		$value = $this->store->get($key);
		return $value?:$default;
	}

	public function getMany($keys, $default = null)
	{
		return $this->store->many($keys)?:$default;
	}

	public function pull($key, $default = null)
	{
		return tap($this->get($key, $default), function ($value) use ($key) {
			$this->forget($key);
		});
	}

	public function put($key, $value, $ttl = null)
	{
		if ($ttl === null) {
			$ttl = $this->default;
		}
		$this->store->put($key, $value, $ttl);
		$this->store->clearExpired();
	}

	public function set($key, $value, $ttl = null)
	{
		$this->put($key, $value, $ttl);
	}

	public function putMany(array $values, $ttl)
	{
		$this->store->putMany($values, $ttl);
	}

	public function setMultiple($values, $ttl = null)
	{
		$this->putMany(is_array($values) ? $values : iterator_to_array($values), $ttl);
	}

	public function add($key, $value, $ttl)
	{
		if (is_null($this->get($key))) {
			$this->put($key, $value, $ttl);

			return true;
		}

		return false;
	}

	public function increment($key, $value = 1)
	{
		return $this->store->increment($key, $value);
	}

	public function decrement($key, $value = 1)
	{
		return $this->store->decrement($key, $value);
	}

	public function forever($key, $value)
	{
		$this->store->forever($key, $value);
	}

	//有则获取　无则存储
	public function remember($key, $ttl, Closure $callback)
	{
		$value = $this->get($key);
		if (! is_null($value)) {
			return $value;
		}
		$this->put($key, $value = $callback(), $ttl);
		return $value;
	}

	public function rememberForever($key, Closure $callback)
	{
		$value = $this->get($key);
		if (! is_null($value)) {
			return $value;
		}

		$this->forever($key, $value = $callback());

		return $value;
	}

	public function forget($key)
	{
		return $this->store->forget($key);
	}

	public function delete($key)
	{
		return $this->forget($key);
	}

	public function deleteMultiple($keys)
	{
		foreach ($keys as $key) {
			$this->forget($key);
		}

		return true;
	}

	public function clear()
	{
		return $this->store->flush();
	}

	public function getDefaultCacheTime()
	{
		return $this->default;
	}

	public function setDefaultCacheTime($ttl)
	{
		$this->default = $ttl;

		return $this;
	}

	public function getStore()
	{
		return $this->store;
	}

	public function getExpireAt($key)
	{
		return $this->store->getExpireAt($key);
	}

	public function __clone()
	{
		$this->store = clone $this->store;
	}
}
