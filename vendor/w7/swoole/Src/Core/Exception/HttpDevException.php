<?php

namespace W7\Core\Exception;

use Psr\Http\Message\ResponseInterface;
use Whoops\Exception\Inspector;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class HttpDevException extends HttpException {
	public function render(): ResponseInterface {
		if ((ENV & BACKTRACE) !== BACKTRACE) {
			$content = 'message: ' . $this->getMessage() . "<br/>file: " . $this->getFile() . "<br/>line: " . $this->getLine();
		} else {
			ob_start();
			$render = new PrettyPageHandler();
			$render->handleUnconditionally(true);
			$render->setException($this->getPrevious());
			$render->setInspector(new Inspector($this->getPrevious()));
			$render->setRun(new Run());
			$render->handle();
			$content = ob_get_clean();
		}

		return $this->response->withContent($content);
	}
}