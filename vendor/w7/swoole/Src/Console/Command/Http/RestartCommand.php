<?php

namespace W7\Console\Command\Http;

class RestartCommand extends HttpCommandAbstract {
	protected $description = 'restart the http service';

	protected function handle($options) {
		// TODO: Implement handle() method.
		$this->restart();
	}
}