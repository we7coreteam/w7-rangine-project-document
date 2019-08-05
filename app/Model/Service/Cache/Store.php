<?php

namespace W7\App\Model\Service\Cache;

interface Store
{
	public function get($key);

	public function many(array $keys);

	public function put($key, $value, $seconds);

	public function putMany(array $values, $seconds);

	public function increment($key, $value = 1);

	public function decrement($key, $value = 1);

	public function forever($key, $value);

	public function forget($key);

	public function flush();

	public function clearExpired();

}
