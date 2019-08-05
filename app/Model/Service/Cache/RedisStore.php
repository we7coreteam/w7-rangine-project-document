<?php

namespace W7\App\Model\Service\Cache;

use Closure;
use Exception;
use Illuminate\Cache\RetrievesMultipleKeys;

use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use W7\App\Model\Entity\Cache;
use W7\Core\Cache\Pool\Pool;

class RedisStore implements Store
{
	protected $prefix = 'document_';
	protected $redis;

	public function __construct()
	{
		$redis  = new \Redis();
		$config = iconfig()->getUserAppConfig('cache')['default'] ?? [];
		$result = $redis->connect($config['host'], $config['port'], $config['timeout']);
		if ($result === false) {
			$error = sprintf('Redis connection failure host=%s port=%d', $config['host'], $config['port']);
			throw new \Exception($error);
		}
		if (!empty($config['password'])) {
			$redis->auth($config['password']);
		}
		if (!empty($config['database'])) {
			$redis->select(intval($config['database']));
		}
		$this->redis = $redis;

	}

	public function connection()
	{
		$config = iconfig()->getUserAppConfig('cache')['default'] ?? [];
		$poolConfig = iconfig()->getUserAppConfig('pool')['cache'] ?? [];
		//未在协程中则不启用连接池
		if (!isCo()) {
			return $this->redis;
		}else{
			if (empty($poolConfig) || empty($poolConfig['enable'])) {
				return $this->redis;
			}else{
				$pool = iloader()->withClass(Pool::class)
					->withSingle()->withAlias('default')
					->withParams(['name' => 'default'])
					->get();
				$pool->setConfig($config);
				$pool->setMaxCount($poolConfig['max']);
				$pool->setCreator($this->redis);

				return $pool->getConnection();
			}
		}
	}

	public function get($key)
	{
		$value = $this->connection()->get($this->prefix.$key);
		return ! is_null($value) ? unserialize($value) : null;
	}

	public function many(array $keys)
	{
		$results = [];

		$values = $this->connection()->mget(array_map(function ($key) {
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
		$this->connection()->setex(
			$this->prefix.$key, (int) max(1, $seconds), serialize($value)
		);
	}

	public function putMany(array $values, $seconds)
	{
		$this->connection()->multi();

		foreach ($values as $key => $value) {
			$this->put($key, $value, $seconds);
		}

		$this->connection()->exec();
	}


	public function increment($key, $value = 1)
	{
		return $this->connection()->incrby($this->prefix.$key, $value);
	}


	public function decrement($key, $value = 1)
	{
		return $this->connection()->decrby($this->prefix.$key, $value);
	}



	public function forever($key, $value)
	{
		$this->connection()->set($this->prefix.$key, serialize($value));
	}

	public function forget($key)
	{
		return (bool) $this->connection()->del($this->prefix.$key);
	}

	public function flush()
	{
		$this->connection()->flushdb();

		return true;
	}

	public function clearExpired()
	{
		return true;
	}



}
