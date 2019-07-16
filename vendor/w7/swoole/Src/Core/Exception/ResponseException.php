<?php
/**
 * @author donknap
 * @date 18-8-29 上午10:31
 */

namespace W7\Core\Exception;

use W7\App;
use Psr\Http\Message\ResponseInterface;

abstract class ResponseException extends \LogicException {
	protected $response;

	public function __construct($message = "", $code = 0, \Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
		$this->response = App::getApp()->getContext()->getResponse();
	}

	abstract public function render() : ResponseInterface;
}