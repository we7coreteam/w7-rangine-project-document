<?php
/**
 * @author donknap
 * @date 18-12-30 下午5:20
 */

namespace W7\Core\Cache\Connection;


interface ConnectionInterface {
	/**
	 * 创建一个连接
	 */
	public function connect(array $config);
}