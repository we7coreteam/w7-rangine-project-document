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

class InternalException extends ResponseExceptionAbstract
{
	public function __construct($message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message, 500, $previous);
	}
}
