<?php
/**
 * @author donknap
 * @date 18-7-30 下午6:27
 */

namespace W7\Core\Pool;

interface PoolInterface {
	/**
	 * 获取一个连接
	 * @return mixed
	 */
	public function getConnection();

	/**
	 * 创建一个连接
	 * @return mixed
	 */
	public function createConnection();

	/**
	 * 释放一个连接
	 * @param $connection 一个资源连接
	 * @return mixed
	 */
	public function releaseConnection($connection);

	/**
	 * 获取当前空闲连接数
	 * @return mixed
	 */
	public function getIdleCount();

	/**
	 * 获取可放的最大数量
	 * @return mixed
	 */
	public function getMaxCount();
}
