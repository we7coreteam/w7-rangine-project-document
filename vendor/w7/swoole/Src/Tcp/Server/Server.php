<?php
/**
 * @author donknap
 * @date 18-11-6 下午2:10
 */

namespace W7\Tcp\Server;

use Swoole\Server as TcpServer;
use W7\Core\Config\Event;
use W7\Core\Server\ServerAbstract;

class Server extends ServerAbstract {
	public $type = parent::TYPE_TCP;

	public function start() {
		$this->server = new TcpServer($this->connection['host'], $this->connection['port'], $this->connection['mode'], $this->connection['sock_type']);
		$this->server->set($this->setting);

		ievent(Event::ON_USER_BEFORE_START, [$this->server]);
		//执行一些公共操作，注册事件等
		$this->registerService();

		$this->server->start();
	}

	/**
	 * @var \Swoole\Server $server
	 * 通过侦听端口的方法创建服务
	 */
	public function listener($server) {
		$tcpServer = $server->addListener($this->connection['host'], $this->connection['port'], $this->connection['sock_type']);
		//tcp需要强制关闭其它协议支持，否则继续父服务
		$tcpServer->set([
			'open_http2_protocol' => false,
			'open_http_protocol' => false,
			'open_websocket_protocol' => false,
		]);
		$event = \iconfig()->getEvent()[parent::TYPE_TCP];
		foreach ($event as $eventName => $class) {
			if (empty($class)) {
				continue;
			}
			$object = \iloader()->singleton($class);
			$tcpServer->on($eventName, [$object, 'run']);
		}
	}
}