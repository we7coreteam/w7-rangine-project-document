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

namespace W7\App\Controller\Message;

use W7\App\Controller\BaseController;
use W7\App\Model\Logic\Message\MessageLogic;

class MessageController extends BaseController
{
	protected function block()
	{
		return new MessageLogic();
	}
}
