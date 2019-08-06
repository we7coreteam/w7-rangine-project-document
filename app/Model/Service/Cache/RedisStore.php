<?php

namespace W7\App\Model\Service\Cache;


class RedisStore implements Store
{
	protected $prefix = 'document_';

	public function get($key)
	{
		$value = icache()->call('get',[$this->prefix.$key]);
		return ! is_null($value) ? unserialize($value) : null;
	}

	public function many(array $keys)
	{
		$results = [];

		$values = icache()->mget(array_map(function ($key) {
			return $this->prefix.$key;
		}, $keys));

		foreach ($values as $index => $value) {
			$results[$keys[$index]] = ! is_null($value) ? unserialize($value) : null;
		}

		return $results;
	}


	public function put($key, $value, $seconds)
	{
		if($seconds <= 0){
			$seconds = 52560000;
		}
		icache()->setex(
			$this->prefix.$key, (int) max(1, $seconds), serialize($value)
		);
	}

	public function putMany(array $values, $seconds)
	{
		icache()->multi();

		foreach ($values as $key => $value) {
			$this->put($key, $value, $seconds);
		}

		icache()->exec();
	}


	public function increment($key, $value = 1)
	{
		return icache()->incrby($this->prefix.$key, $value);
	}


	public function decrement($key, $value = 1)
	{
		return icache()->decrby($this->prefix.$key, $value);
	}



	public function forever($key, $value)
	{
		icache()->call('set',[$this->prefix.$key, serialize($value)]);
	}

	public function forget($key)
	{
		return (bool) icache()->del($this->prefix.$key);
	}

	public function flush()
	{
		icache()->flushdb();
		return true;
	}

	public function clearExpired()
	{
		return true;
	}

	public function getExpireAt($key)
	{
		$ttl = icache()->call('TTL',[$this->prefix.$key]);
		if($ttl >= 0){
			return $ttl + time();
		}
	}

}
