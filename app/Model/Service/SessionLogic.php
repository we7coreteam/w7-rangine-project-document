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

namespace W7\App\Model\Service;

use W7\App\Model\Entity\Session;
use W7\Core\Database\LogicAbstract;
use W7\Core\Helper\Traiter\InstanceTraiter;

class SessionLogic extends LogicAbstract
{
	use InstanceTraiter;

	public function getBySessionId($sessionId)
	{
		return Session::query()->where('session_id', '=', $sessionId)->first();
	}

	public function deleteBySessionId($sessionId)
	{
		return Session::query()->where('session_id', '=', $sessionId)->delete();
	}
}
