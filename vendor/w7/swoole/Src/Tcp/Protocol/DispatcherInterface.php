<?php

namespace W7\Tcp\Protocol;

use Swoole\Server;

interface DispatcherInterface {
	public function dispatch (Server $server, $fd, $data);
}