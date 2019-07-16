<?php

namespace W7\Core\Exception;

use Psr\Http\Message\ResponseInterface;

class HttpReleaseException extends HttpException {
	public function render(): ResponseInterface {
		return $this->response->json(['error' => '系统内部错误'], 500);
	}
}