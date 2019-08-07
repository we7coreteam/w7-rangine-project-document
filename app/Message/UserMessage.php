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

namespace W7\App\Message;

use W7\Core\Message\MessageAbstract;
use W7\Core\Message\MessageTraiter;

class UserMessage extends MessageAbstract
{
	use MessageTraiter;

	public $messageType = 'user';

	public $uid;

	public $username;

	public function isBoy()
	{
		return true;
	}
}
