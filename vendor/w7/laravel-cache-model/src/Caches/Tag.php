<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 12:46
 */

namespace W7\Laravel\CacheModel\Caches;

/**
 * 实现数据库缓存命名空间
 * Class Tag
 * @package W7\Laravel\CacheModel
 */
class Tag
{
	const PREFIX = 'we7_model_cache';
	
	/**
	 * @param string $namespace
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function flush($namespace)
	{
		Cache::singleton()->del(static::getRootNamespace($namespace));
	}
	
	private static function getRootNamespace($namespace)
	{
		return join(':', [static::PREFIX, $namespace]);
	}
	
	/**
	 * 获取缓存的键值
	 * @param $key
	 * @param $namespace
	 * @return string
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function getCacheKey($key, $namespace)
	{
		$namespace = static::getRootNamespace($namespace);
		
		$pieces = explode(':', $namespace);
		
		$cacheKey = static::getPrefix($pieces) . ':' . $key;
		
		return $cacheKey;
	}
	
	/**
	 * a:b:c:d
	 *
	 * a     => k1
	 * a:b   => k2
	 * a:b:c => k3
	 *
	 * a:b:c:N => (a:b:c):N => k3:N => cache get
	 *
	 * @param $pieces
	 * @return mixed
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function getPrefix($pieces)
	{
		// $cache = Cache::singleton();
		$cache = Cache::getCacheResolver();
		
		$length = count($pieces);
		for ($i = 0; $i < $length; $i++) {
			$key   = Tag::joinPieces($pieces, $i + 1);
			$value = $cache->get($key);
			
			if (empty($value)) {
				// 'a:b'
				$pre_key   = Tag::joinPieces($pieces, $i);// 'a'
				$pre_value = $cache->get($pre_key) ?? '';// 'a' => value
				
				$suffix = $pieces[$i];// 'b'
				
				$value = static::hash($pre_value, $suffix);
				$cache->set($key, $value, Cache::FOREVER);
			}
		}
		
		return $cache->get(join(':', $pieces));
	}
	
	/**
	 * 前 n 个元素用 ':' 拼接
	 * @param $pieces
	 * @param $n
	 * @return string
	 */
	private static function joinPieces($pieces, $n)
	{
		$length = count($pieces);
		$array  = [];
		for ($i = 0; $i < $n && $n >= 0 && $n <= $length; $i++) {
			$array[] = $pieces[$i];
		}
		return join(':', $array);
	}
	
	/**
	 * @param mixed ...$contents
	 * @return string
	 */
	private static function hash(...$contents)
	{
		$params   = func_get_args();
		$params[] = str_random(8);
		
		return md5(uniqid(join(':', $params)));
	}
}