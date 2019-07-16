<?php

namespace W7\Console\Command\Tcp;

class StartCommand extends TcpCommandAbstract {
	protected $description = 'start tcp service';

	protected function handle($options) {
		$this->start($options);
	}
}