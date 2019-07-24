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

namespace W7\App\Model\Logic;

class VerificationcodeLogic extends BaseLogic
{
	public function addCode($key, $flight, $time)
	{
		return $this->set($key, $flight, $time);
	}

	public function getCode($key)
	{
		return $this->get($key);
	}
}
