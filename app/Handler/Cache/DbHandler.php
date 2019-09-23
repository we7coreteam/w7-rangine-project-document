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

use W7\App\Model\Entity\Cache;
use W7\Core\Cache\Handler\HandlerAbstract;

class DbHandler extends HandlerAbstract
{
	private $getValue = '';

	public static function getHandler($config): HandlerAbstract
	{
		//改为获取数据库实例
		return idb();
	}

	public function set($key, $value, $ttl = null)
	{
		if (!$key || !$value) {
			return false;
		}

		$ttl = $this->getTtl($ttl);

		$cache = new Cache();
		$cache->key = $key;
		$cache->value = $this->serialize($value);
		$cache->expired_at = time() + $ttl;
		$result = $cache->save();
		if ($result) {
			return true;
		}
		return false;
	}

	public function get($key, $default = null)
	{
		if (!$key) {
			return false;
		}

		if ($this->has($key) == 1) {
			if ($this->getValue === false || $this->getValue === null) {
				return $default;
			}
			return $this->unserialize($this->getValue);
		}
		return $default;
	}

	public function has($key)
	{
		if (!$key) {
			return -1;
		}

		$value = Cache::query()->where('key', $key)->first();
		if ($value) {
			$this->getValue = $value;
			if ($value['expired_at'] >= time()) {
				return 1;
			} else {
				$this->delete($key);
			}
		}
		return -1;
	}

	public function setMultiple($values, $ttl = null)
	{
		//设置多个缓存
		/*
		[
			'key' => 'value',
			'key1' => 'value1'
		]
		*/
		if (is_array($values)) {
			$ttl = $this->getTtl($ttl);
			idb()->beginTransaction();
			foreach ($values as $key => $val) {
				$result = $this->set($key, $val, time() + $ttl);
				if ($result === false) {
					idb()->rollBack();
				}
			}
			idb()->commit();
			return true;
		}
		return false;
	}

	public function getMultiple($keys, $default = null)
	{
		//获取多个缓存
		//返回的数据格式为
		/*
		[
			'value',
			'value1'
		]
		*/
		$values = [];
		if (is_array($keys)) {
			foreach ($keys as $key => $val) {
				$values[] = $this->get($val);
			}
		}
		return $values;
	}

	public function delete($key)
	{
		if (!$key) {
			return true;
		}

		if ($this->has($key) == 1) {
			return Cache::query()->where('key', $key)->delete();
		}
		return false;
	}

	public function deleteMultiple($keys)
	{
		if (is_array($keys)) {
			idb()->beginTransaction();
			foreach ($keys as $key => $val) {
				$result = $this->delete($val);
				if ($result === false) {
					idb()->rollBack();
				}
			}
			idb()->commit();
			return true;
		}
		return false;
	}

	public function clear()
	{
		return Cache::query()->delete();
	}

	private function getValue($key) {
		$result = Cache::query()->where('key', $key)->first();
		if ($result) {
			$this->getValue = $result;
		}
	}

	private function getTtl($ttl): int {
		return ($ttl === null) ? 0 : (int)$ttl;
	}

	private function unserialize($data) {
		return is_numeric($data) ? $data : unserialize($data);
	}

	private function serialize($data) {
		return is_numeric($data) ? $data : serialize($data);
	}
}
