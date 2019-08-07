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

namespace W7\App\Event;

use W7\Core\Helper\Traiter\InstanceTraiter;

class ChangeAuthEvent extends Event
{
	use InstanceTraiter;
	public $eventType = 'change_auth';
	public $user_id;
	public $document_id;
}
