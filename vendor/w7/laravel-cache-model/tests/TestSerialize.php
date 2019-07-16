<?php
/**
 * Created by PhpStorm.
 * User: gorden
 * Date: 19-3-7
 * Time: 上午11:32
 */

namespace W7\Laravel\CacheModel\Tests;


use Redis;

class TestSerialize extends TestCase
{
	public function testRedis() {
		$redis = new Redis();
		$redis->connect('127.0.0.1', 6379);
		echo "Connection to server sucessfully";
		//存储数据到列表中
		$redis->lpush("tutorial-list", "Redis");
		$redis->lpush("tutorial-list", "Mongodb");
		$redis->lpush("tutorial-list", "Mysql");
		// 获取存储的数据并输出
		$arList = $redis->lrange("tutorial-list", 0 ,5);
		echo "Stored string in redis";
		print_r($arList);
	}
}