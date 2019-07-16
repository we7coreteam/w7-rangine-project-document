<?php
/**
 * @author donknap
 * @date 18-10-23 下午3:40
 */

namespace W7\Core\Pool;


use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

abstract class CoPoolAbstract implements PoolInterface {
	protected $poolName;
	/**
	 * 最大连接数据
	 * @var int
	 */
	protected $maxActive = 100;

	/**
	 * 执行中连接队列
	 * @var \SplQueue $busyQueue
	 */
	protected $busyCount;

	/**
	 * 空间连接队列
	 * @var \SplQueue $idleQueue
	 */
	protected $idleQueue;

	/**
	 * 挂起协程ID队列，恢复时按顺序恢复
	 * @var \SplQueue
	 */
	protected $waitQueue;

	/**
	 * 等待数
	 * @var int
	 */
	protected $waitCount = 0;

	protected $config;

	public function __construct($name = '') {
		$this->poolName = $name;

		$this->busyCount = 0;
		$this->waitCount = 0;

		$this->waitQueue = new \SplQueue();
		$this->idleQueue = new Channel($this->maxActive);
	}

	abstract function createConnection();

	public function getConnection() {

		if ($this->getIdleCount() > 0) {
			ilogger()->channel('database')->debug($this->poolName . ' get by queue , count ' . $this->getIdleCount());

			$connect = $this->getConnectionFromPool();
			$this->busyCount++;
			return $connect;
		}

		//如果 空闲队列数+执行队列数 等于 最大连接数，则挂起协程
		if ($this->busyCount + $this->getIdleCount() >= $this->getMaxCount()) {
			//等待进程数++
			$this->waitCount++;

			ilogger()->channel('database')->debug($this->poolName . ' suspend connection , count ' . $this->idleQueue->length() . '. wait count ' . $this->waitCount);

			if ($this->suspendCurrentCo() == false) {
				//挂起失败时，抛出异常，恢复等待数
				$this->waitCount--;
				throw new \RuntimeException('Reach max connections! Cann\'t pending fetch!');
			}
			//回收连接时，恢复了协程，则从空闲中取出连接继续执行
			ilogger()->channel('database')->debug($this->poolName . ' resume connection , count ' . $this->idleQueue->length());
		}

		$connect = $this->createConnection();
		$this->busyCount++;
		ilogger()->channel('database')->debug($this->poolName . ' create connection , count ' . $this->idleQueue->length() . '. busy count ' . $this->busyCount);

		return $connect;
	}

	public function releaseConnection($connection) {
		$this->busyCount--;
		if ($this->getIdleCount() < $this->getMaxCount()) {

			$this->setConnectionFormPool($connection);
			ilogger()->channel('database')->debug($this->poolName . ' release push connection , count ' . $this->idleQueue->length() . '. busy count ' . $this->busyCount);

			if ($this->waitCount > 0) {
				$this->waitCount--;
				$this->resumeCo();
			}
			return true;
		}
	}

	public function getIdleCount() {
		return $this->idleQueue->length();
	}

	public function getMaxCount() {
		return $this->maxActive;
	}

	/**
	 * @param int $maxActive
	 */
	public function setMaxCount(int $maxActive) {
		$this->maxActive = $maxActive;
	}

	public function setConfig($config) {
		$this->config = $config;
	}

	/**
	 * 挂起当前协程，以便之后恢复
	 */
	private function suspendCurrentCo() {
		$coid = Coroutine::getuid();
		$this->waitQueue->push($coid);
		return Coroutine::suspend($coid);
	}

	/**
	 * 从队列里恢复一个挂起的协程继续执行
	 * @return bool
	 */
	private function resumeCo() {
		$coid = $this->waitQueue->shift();
		if (!empty($coid)) {
			Coroutine::resume($coid);
		}
		return true;
	}

	private function getConnectionFromPool() {
		return $this->idleQueue->pop();
	}

	private function setConnectionFormPool($connection) {
		return $this->idleQueue->push($connection);
	}
}