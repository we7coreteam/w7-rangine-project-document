<?php

namespace W7\Console\Command\Tcp;

class StopCommand extends TcpCommandAbstract {
	protected $description = 'stop tcp service';

	protected function handle($options) {
		$this->stop();
	}
}