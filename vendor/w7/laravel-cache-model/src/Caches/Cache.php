<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 13:34
 */

namespace W7\Laravel\CacheModel\Caches;


use Psr\SimpleCache\CacheInterface;
use stdClass;
use W7\Laravel\CacheModel\Exceptions\InvalidArgumentException;

/**
 * Class Cache
 * @package W7\Laravel\CacheModel
 */
class Cache
{
	const FOREVER = 3153600000; //86400 * 365 * 100;
	
	const NULL = 'nil&null';
	
	private static $needSerialize = null;
	
	/**
	 * @var CacheInterface
	 */
	private static $cacheInterfaceSingleton = null;
	
	/**
	 * @param CacheInterface $cache
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function setCacheResolver(CacheInterface $cache)
	{
		static::$cacheInterfaceSingleton = $cache;
		
		static::needSerialize();
	}
	
	/**
	 * 存取对象是否需要序列化和反序列化
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function needSerialize()
	{
		if (!static::isImplemented()) {
			return false;
		}
		
		if (is_null(static::$needSerialize)) {
			$key = Tag::PREFIX . ':test';
			
			$testObj = new stdClass();
			
			static::$cacheInterfaceSingleton->set($key, $testObj, 1);
			$get = static::$cacheInterfaceSingleton->get($key);
			
			static::$needSerialize = !is_object($get);
			
			static::$cacheInterfaceSingleton->delete($key);
		}
		
		return static::$needSerialize;
	}
	
	/**
	 * @return bool
	 */
	public static function isImplemented()
	{
		return !empty(static::$cacheInterfaceSingleton);
	}
	
	/**
	 * @return CacheInterface
	 * @throws InvalidArgumentException
	 */
	public static function getCacheResolver()
	{
		if (!static::$cacheInterfaceSingleton instanceof CacheInterface) {
			throw new InvalidArgumentException('使用 Model Cache 必须先调用 \W7\Laravel\CacheModel\Caches\Cache::setCacheResolver($cache)');
		}
		return static::$cacheInterfaceSingleton;
	}
	
	/**
	 * @var static
	 */
	private static $singleton;
	
	/**
	 * 获取单例
	 * @return Cache
	 */
	public static function singleton()
	{
		return static::$singleton ?? (static::$singleton = new static());
	}
	
	/**
	 * @return CacheInterface
	 * @throws InvalidArgumentException
	 */
	public function getCache()
	{
		return static::getCacheResolver();
	}
	
	/**
	 * Serialize the value.
	 *
	 * @param mixed $value
	 * @return mixed
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function serialize($value)
	{
		if (!static::needSerialize()) {
			return $value;
		}
		return serialize($value);
	}
	
	/**
	 * Unserialize the value.
	 *
	 * @param mixed $value
	 * @return mixed
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function unserialize($value)
	{
		if (!static::needSerialize()) {
			return $value;
		}
		return unserialize($value);
	}
	
	protected function isValidData($var)
	{
		return is_numeric($var) || is_object($var) || is_null($var);
	}
	
	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 * @throws InvalidArgumentException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function set($key, $value, $ttl = null)
	{
		$value = $this->serialize($value);
		
		$this->getCache()->set($key, $value, $ttl ?? static::FOREVER);
	}
	
	/**
	 * @param $key
	 * @return mixed
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function get($key)
	{
		$value = $this->getCache()->get($key);
		
		$value = $this->unserialize($value);
		
		// jd($key, $value);
		
		if ($this->isValidData($value)) {
			return $value;
		} else {
			return null;
		}
	}
	
	/**
	 * 没有模型也可能有缓存，防止缓存击穿
	 * @param string $key
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function has($key)
	{
		return $this->getCache()->has($key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function del($key)
	{
		return $this->getCache()->delete($key);
	}
	
	/**
	 * 清空当前表的缓存
	 * @param string $namespace
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function flush($namespace = '')
	{
		Tag::flush($namespace);
	}
	
	/**
	 * @param string         $key
	 * @param stdClass|null $model
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function setModel($key, $model)
	{
		$model = $model ?? static::NULL;
		
		$this->set($key, $model);
	}
	
	/**
	 * 获取缓存中键为 $key 的记录
	 * @param $key
	 * @return stdClass|null
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getModel($key)
	{
		$model = $this->get($key);
		if ($model === static::NULL) {
			$model = null;
		}
		return $model;
	}
	
	/**
	 * @param string $key
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function delModel($key)
	{
		return $this->del($key);
	}
	
	/**
	 * 缓存中是否存在主键为 key 的记录
	 * @param $key
	 * @return bool
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function hasModel($key)
	{
		$model = $this->getModel($key);
		
		return !empty($model);
	}
}