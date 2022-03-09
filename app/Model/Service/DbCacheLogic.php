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

namespace W7\App\Model\Service;

use W7\App\Model\Entity\Cache;
use W7\Core\Database\LogicAbstract;
use W7\Core\Helper\Traiter\InstanceTraiter;

class DbCacheLogic extends LogicAbstract
{
	use InstanceTraiter;

	public function set($key, $value, $ttl = null)
	{
		if ($ttl) {
			$ttl = time() + $ttl;
		}
		$row = Cache::query()->where('key', '=', $key)->first();
		if ($row) {
			$row->update(['value' => $value, 'expired_at' => $ttl ?? 0]);
		} else {
			Cache::create(['key' => $key, 'value' => $value, 'expired_at' => $ttl ?? 0]);
		}
		return true;
	}

	public function get($key, $default = null)
	{
		$row = Cache::query()->where('key', '=', $key)->first();
		$ret = $row ? $row->value : $default;
		if ($row && $row->expired_at && $row->expired_at < time()) {
			$row->delete();
			$ret = false;
		}
		return $ret;
	}

	public function has($key)
	{
		$row = $this->get($key);
		return $row ? true : false;
	}

	public function setMultiple($values, $ttl = null)
	{
		foreach ($values as $k => $v) {
			$this->set($k, $v, $ttl);
		}
		return true;
	}

	public function getMultiple($keys, $default = null)
	{
		$data = [];
		foreach ($keys as $k) {
			$data[] = $this->get($k) ?? $default;
		}
		return $data;
	}

	public function delete($key)
	{
		$ret = false;
		$row = Cache::query()->where('key', '=', $key)->first();
		if ($row) {
			$row->delete();
			$ret = true;
		}
		return $ret;
	}

	public function deleteMultiple($keys)
	{
		foreach ($keys as $k) {
			$this->delete($k);
		}
		return true;
	}

	public function clear()
	{
		Cache::query()->truncate();
		return true;
	}
}
