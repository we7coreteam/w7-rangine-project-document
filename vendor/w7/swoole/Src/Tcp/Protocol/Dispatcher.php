<?php

namespace W7\Tcp\Protocol;

use Swoole\Server;
use W7\Tcp\Protocol\Thrift\Dispatcher as ThriftDispatcher;
use W7\Tcp\Protocol\Json\Dispatcher as JsonDispatcher;

class Dispatcher {
	private static $protocolMap = [
		'thrift' => ThriftDispatcher::class,
		'json' => JsonDispatcher::class
	];

	/**解析data数据，并转到对应的控制器下
	 * @param $protocol
	 * @param Server $server
	 * @param $fd
	 * @param $data
	 */
	public static function dispatch($protocol, Server $server, $fd, $data) {
		$dispatcher = self::getDispatcher($protocol);
		$dispatcher->dispatch($server, $fd, $data);
	}

	private static function getDispatcher($protocol) : DispatcherInterface {
		if (empty(self::$protocolMap[$protocol])) {
			$protocol = 'json';
		}

		return \iloader()->singleton(self::$protocolMap[$protocol]);
	}
}