<?php

namespace W7\Console\Command\Http;

class StartCommand extends HttpCommandAbstract {
	protected $description = 'start the http service';

	protected function configure() {
		$this->addOption('--enable-tcp', null, null, 'enable tcp service');
	}

	protected function handle($options) {
		$this->start($options);
	}
}