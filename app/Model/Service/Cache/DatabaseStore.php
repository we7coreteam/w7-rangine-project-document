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
use Exception;
use Illuminate\Cache\RetrievesMultipleKeys;

use Illuminate\Support\InteractsWithTime;
use W7\App\Model\Entity\Cache;

class DatabaseStore implements Store
{
	use InteractsWithTime, RetrievesMultipleKeys;

	protected $prefix = 'document_';

	public function get($key)
	{
		$prefixed = $this->prefix.$key;

		$cache = Cache::where('key', '=', $prefixed)->first();
		if (is_null($cache)) {
			return;
		}

		$cache = is_array($cache) ? (object) $cache : $cache;

		if ($this->getTime() >= $cache->expired_at) {
			$this->forget($key);

			return;
		}

		return unserialize($cache->value);
	}

	public function put($key, $value, $seconds)
	{
		if ($seconds <= 0) {
			$seconds = 52560000;
		}
		$key = $this->prefix.$key;

		$value = serialize($value);

		$expired_at = $this->getTime() + (int) $seconds;
		if(Cache::where('key', $key)->first()){
			Cache::where('key', $key)->update(compact('value', 'expired_at'));
		}else{
			Cache::create(compact('key', 'value', 'expired_at'));
		}

//		try {
//			Cache::create(compact('key', 'value', 'expired_at'));
//		} catch (Exception $e) {
//			Cache::where('key', $key)->update(compact('value', 'expired_at'));
//		}
	}

	public function increment($key, $value = 1)
	{
		return $this->incrementOrDecrement($key, $value, function ($current, $value) {
			return $current + $value;
		});
	}

	public function decrement($key, $value = 1)
	{
		return $this->incrementOrDecrement($key, $value, function ($current, $value) {
			return $current - $value;
		});
	}

	protected function incrementOrDecrement($key, $value, Closure $callback)
	{
		return idb()->transaction(function () use ($key, $value, $callback) {
			$prefixed = $this->prefix.$key;

			$cache = Cache::where('key', $prefixed)
				->lockForUpdate()->first();

			if (is_null($cache)) {
				return false;
			}

			$cache = is_array($cache) ? (object) $cache : $cache;

			$current = unserialize($cache->value);

			$new = $callback((int) $current, $value);

			if (! is_numeric($current)) {
				return false;
			}

			Cache::where('key', $prefixed)->update([
				'value' => serialize($new),
			]);

			return $new;
		});
	}

	protected function getTime()
	{
		return $this->currentTime();
	}

	public function forever($key, $value)
	{
		$this->put($key, $value, 0);
	}

	public function forget($key)
	{
		Cache::where('key', '=', $this->prefix.$key)->delete();

		return true;
	}

	public function flush()
	{
		return (bool) Cache::where('id', '>', 0)->delete();
	}

	public function clearExpired()
	{
		Cache::where('expired_at', '<', $this->getTime())->delete();
	}

	public function getExpireAt($key)
	{
		$prefixed = $this->prefix.$key;

		$cache = Cache::where('key', '=', $prefixed)->first();
		if (is_null($cache)) {
			return -2;
		}

		if ($this->currentTime() >= $cache->expired_at) {
			return -2;
		}

		return $cache->expired_at;
	}
}
