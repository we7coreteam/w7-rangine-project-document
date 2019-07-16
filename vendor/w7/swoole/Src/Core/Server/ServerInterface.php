<?php
/**
 * 服务对象接口
 * @author donknap
 * @date 18-7-20 上午9:22
 */

namespace W7\Core\Server;

interface ServerInterface {
	/**
	 * 服务启动
	 */
	public function start();

	/**
	 * 服务停止
	 * @return mixed
	 */
	public function stop();

	/**
	 * 服务是否运行
	 * @return mixed
	 */
	public function isRun();

	/**
	 * 获取服务对象
	 * @return mixed
	 */
	public function getServer();

	/**
	 * 获取服务状态
	 * @return mixed
	 */
	public function getStatus();
}
