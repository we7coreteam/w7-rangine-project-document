<?php
/**
 * @author donknap
 * @date 18-7-20 上午9:14
 */

namespace W7\Http\Server;

use Swoole\Http\Server as HttpServer;
use W7\Core\Server\ServerAbstract;
use W7\Core\Config\Event;

class Server extends ServerAbstract {
	public $type = parent::TYPE_HTTP;

	public function start() {
		if (!empty($this->setting['open_http2_protocol'])) {
			$this->connection['type'] = SWOOLE_SOCK_TCP|SWOOLE_SSL;
		}
		$this->server = $this->getServer();
		$this->server->set($this->setting);

		ievent(Event::ON_USER_BEFORE_START, [$this->server]);
		//执行一些公共操作，注册事件等
		$this->registerService();

		$this->server->start();
	}

	public function getServer() {
		if (empty($this->server)) {
			$this->server = new HttpServer($this->connection['host'], $this->connection['port'], $this->connection['mode'], $this->connection['sock_type']);
		}
		return $this->server;
	}
}
