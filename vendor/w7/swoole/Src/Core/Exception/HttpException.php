<?php
/**
 * @author donknap
 * @date 18-8-24 下午4:33
 */
namespace W7\Core\Exception;

use Psr\Http\Message\ResponseInterface;

class HttpException extends TcpException {
	public function render(): ResponseInterface {
		return $this->response->json(['error' => $this->getMessage()], $this->getCode());
	}
}