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

namespace W7\App\Handler\Session;

use W7\App\Model\Entity\Session;
use W7\App\Model\Service\SessionLogic;
use W7\Core\Session\Handler\HandlerAbstract;

class DbHandler extends HandlerAbstract
{
	public function read($session_id)
	{
		if (empty($session_id)) {
			return '';
		}
		$session = SessionLogic::instance()->getBySessionId($session_id);
		if (empty($session) || $session->expired_at < time()) {
			return '';
		}
		return $session->data;
	}

	public function destroy($session_id)
	{
		return SessionLogic::instance()->deleteBySessionId($session_id);
	}

	public function write($session_id, $session_data)
	{
		$session = SessionLogic::instance()->getBySessionId($session_id);
		if (empty($session)) {
			Session::query()->create([
				'session_id' => $session_id,
				'data' => $session_data,
				'expired_at' => time() + $this->getExpires(),
			]);
		} else {
			$session->data = $session_data;
			$session->expired_at = time() + $this->getExpires();
			$session->save();
		}
		return true;
	}

	public function gc($maxlifetime)
	{
		Session::query()->where('expired_at', '<', time() - $maxlifetime)->delete();
	}
}
