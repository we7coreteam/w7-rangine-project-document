<?php

/**
 * WeEngine Document System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\App\Exception;

use Psr\Http\Message\ResponseInterface;
use W7\Core\Exception\ResponseExceptionAbstract;

class ErrorHttpException extends ResponseExceptionAbstract
{
	protected $data;

	public function __construct($message = '', $data = [], $code = 0, \Throwable $previous = null)
	{
		if (empty($code)) {
			$code = '500';
		}
		$this->data = $data;
		parent::__construct($message, $code, $previous);
	}

	public function render(): ResponseInterface
	{
		return $this->response->withStatus(200)->withContent(json_encode([
			'status' => false,
			'code' => $this->getCode(),
			'data' => $this->data,
			'message' => $this->getMessage(),
		],JSON_UNESCAPED_UNICODE));
	}
}
