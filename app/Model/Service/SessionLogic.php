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

use mysql_xdevapi\Exception;

class SessionLogic extends BaseLogic
{
	protected $token;

	public function __construct($token)
	{
		if(!$token){
			throw new \Exception('请登录后再使用session');
		}
		$this->token = $token;
	}

	public function get($key)
	{
		return cache()->get($this->getKey($key));
	}

	public function set($key, $value)
	{
		cache()->set($this->getKey($key), $value, $this->getTtl());
	}

	public function delete($key)
	{
		return cache()->pull($this->getKey($key));
	}

	public function getKey($key)
	{
		return $this->token.'_'.$key;
	}

	public function getTtl()
	{
		return cache()->getExpireAt($this->token) - time();
	}
}
