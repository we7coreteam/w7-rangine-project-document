<?php

namespace W7\Console\Command\Http;

use W7\Core\Server\ServerCommandAbstract;
use W7\Http\Server\Server;

abstract class HttpCommandAbstract extends ServerCommandAbstract {
	protected function createServer() {
		return new Server();
	}
}