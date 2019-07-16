<?php

namespace W7\Console\Command\Tcp;

use W7\Core\Server\ServerCommandAbstract;
use W7\Tcp\Server\Server;

abstract class TcpCommandAbstract extends ServerCommandAbstract {
	protected function createServer() {
		return new Server();
	}
}