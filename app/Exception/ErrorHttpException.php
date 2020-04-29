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

use W7\Core\Exception\ResponseExceptionAbstract;

class ErrorHttpException extends ResponseExceptionAbstract
{
	protected $data;

	public function __construct($message = '', $data = [], $code = 0, \Throwable $previous = null)
	{
		if (empty($code)) {
			$code = '500';
		}
		$message = json_encode([
			'status' => false,
			'code' => $code,
			'data' => $data,
			'message' => $message,
		]);
		parent::__construct($message, 200, $previous);
	}
}
