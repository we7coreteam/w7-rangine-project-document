<?php


namespace W7\Laravel\CacheModel\Caches;


use Illuminate\Support\Collection;
use W7\Laravel\CacheModel\Exceptions\InvalidArgumentException;

class BatchCache
{
	private $cache = null;
	
	private $namespace = 'batch_cache';
	
	/**
	 * BatchCache constructor.
	 * @param Cache  $cache
	 * @param string $namespace
	 */
	public function __construct(Cache $cache, $namespace = 'batch_cache')
	{
		$this->cache     = $cache;
		$this->namespace = $namespace;
	}
	
	protected function getCache()
	{
		return $this->cache;
	}
	
	/**
	 * @param string $key
	 * @return string
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function getCacheKey($key)
	{
		return Tag::getCacheKey($key, $this->namespace);
	}
	
	/**
	 * @param $key
	 * @return string
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function getSizeCacheKey($key)
	{
		return $this->getCacheKey("{$key}:size");
	}
	
	/**
	 * @param $key
	 * @param $index
	 * @return string
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function getIndexCacheKey($key, $index)
	{
		return $this->getCacheKey("{$key}:{$index}");
	}
	
	/**
	 * @param string $key
	 * @return int
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function getSize($key)
	{
		$sizeCacheKey = $this->getSizeCacheKey($key);
		
		return $this->getCache()->get($sizeCacheKey);
	}
	
	/**
	 * @param $key
	 * @param $value
	 * @param $ttl
	 * @throws InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function setSize($key, $value, $ttl)
	{
		$sizeCacheKey = $this->getSizeCacheKey($key);
		
		$this->getCache()->set($sizeCacheKey, $value, $ttl);
	}
	
	/**
	 * @param $key
	 * @param $i
	 * @param $item
	 * @param $ttl
	 * @throws InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function setItem($key, $i, $item, $ttl)
	{
		$indexCacheKey = $this->getIndexCacheKey($key, $i);
		
		$this->getCache()->set($indexCacheKey, $item, $ttl);
	}
	
	/**
	 * @param $key
	 * @param $index
	 * @return mixed
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function getItem($key, $index)
	{
		$indexCacheKey = $this->getIndexCacheKey($key, $index);
		
		return $this->getCache()->get($indexCacheKey);
	}
	
	/**
	 * @param string   $key
	 * @param array    $array
	 * @param int|null $ttl
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function set($key, $array, $ttl = null)
	{
		$size = count($array);
		
		$this->setSize($key, $size, $ttl);
		
		$cache = $this->getCache();
		foreach ($array as $index => $item) {
			$cacheKey = $this->getIndexCacheKey($key, $index);
			
			$cache->set($cacheKey, $item, $ttl);
		}
	}
	
	/**
	 * @param $key
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function flush($key)
	{
		$this->setSize($key, 0, 0);
	}
	
	/**
	 * 返回值为 false, 数据损坏
	 * @param $key
	 * @return bool|Collection|array
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function get($key)
	{
		$cacheValue = [];
		
		$size = $this->getSize($key);
		if (empty($size)) {
			return null;
		}
		
		$realSize = 0;
		for ($i = 0; $i < $size; $i++) {
			$cacheItemKey   = $this->getIndexCacheKey($key, $i);
			$cacheItemValue = $this->getCache()->get($cacheItemKey);
			if (empty($cacheItemValue)) {
				continue;
			}
			$realSize++;
			$cacheValue[] = $cacheItemValue;
		}
		
		if ($realSize != $size) {
			return false;
		}
		
		return $cacheValue;
	}
	
	/**
	 * @param $key
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function delete($key)
	{
		$this->getCache()->del($this->getSizeCacheKey($key));
	}
}