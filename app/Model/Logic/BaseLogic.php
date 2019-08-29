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

namespace W7\App\Model\Logic;

use W7\Core\Cache\Cache;
use W7\Core\Database\LogicAbstract;

class BaseLogic extends LogicAbstract
{
	private $cache = null;
	private $prefix = 'document_logic_';

	//获取缓存
	public function get($key, $default=null)
	{
		return $this->getCache()->get($this->generateKey($key), $default);
	}

	public function increment($key, $ttl=24*3600, $step=1)
	{
		$value = $this->get($key);
		if ($value) {
			$value = intval($value) + intval($step);
			$this->set($key, $value);
		} else {
			$this->set($key, 1, $ttl);
		}
		return true;
	}

	public function decrement($key, $ttl, $step=1)
	{
		$value = $this->get($key);
		if ($value) {
			$value = intval($value) - intval($step);
			$this->set($key, $value);
			return true;
		}
		return false;
	}

	//设置缓存
	public function set($key, $value, $ttl=24*3600)
	{
		return $this->getCache()->set($this->generateKey($key), $value, $ttl);
	}

	//删除缓存
	public function delete($key)
	{
		return $this->getCache()->delete($this->generateKey($key));
	}

	public function getCache()
	{
		if (!$this->cache) {
			$this->cache = new Cache();
		}
		return $this->cache;
	}

	public function generateKey($key)
	{
		return $this->prefix.$key;
	}
}
