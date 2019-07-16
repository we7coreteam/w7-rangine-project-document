<?php

namespace W7\Console\Command\Http;

class StopCommand extends HttpCommandAbstract {
	protected $description = 'stop the http service';

	protected function handle($options) {
		$this->stop();
	}
}