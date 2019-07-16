<?php

namespace W7\Core\Exception;

use Psr\Http\Message\ResponseInterface;

class DumpException extends ResponseException {
	public function render(): ResponseInterface {
		return icontext()->getResponse()->withContent($this->getMessage());
	}
}