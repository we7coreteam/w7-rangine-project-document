<?php

namespace W7\App\Handler\Cache;

use W7\Core\Cache\Handler\HandlerAbstract;

class DbHandler extends HandlerAbstract {
	public static function getHandler($config): HandlerAbstract {
		//改为获取数据库实例
		return idb();
	}

	public function set($key, $value, $ttl = null) {
		//保存数据
	}

	public function get($key, $default = null) {
		//获取数据
	}

	public function has($key) {
		//检测数据是否存在
	}

	public function setMultiple($values, $ttl = null) {
		//设置多个缓存
		/*
		[
			'key' => 'value',
			'key1' => 'value1'
		]
		*/
	}

	public function getMultiple($keys, $default = null) {
		//获取多个缓存
		//返回的数据格式为
		/*
		[
			'value',
			'value1'
		]
		*/
	}

	public function delete($key) {
		//删除缓存
	}

	public function deleteMultiple($keys) {
		//删除多个缓存
	}

	public function clear() {
		//清空所有缓存
	}
}