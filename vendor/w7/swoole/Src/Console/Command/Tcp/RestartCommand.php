<?php

namespace W7\Console\Command\Tcp;

class RestartCommand extends TcpCommandAbstract {
	protected $description = 'restart tcp service';

	protected function handle($options) {
		// TODO: Implement handle() method.
		$this->restart();
	}
}