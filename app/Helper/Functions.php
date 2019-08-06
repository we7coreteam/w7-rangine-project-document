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

use W7\App;

/**
 * 公共函数，已加载
 */
function cache()
{
	$manager = new App\Model\Service\Cache\CacheManager();
	return $manager->store();
}

function session_open()
{
	$token = icontext()->getContextDataByKey('token');
	icontext()->setContextDataByKey('session', new App\Model\Service\SessionLogic($token));
}

function session($key = null, $value='__default__')
{
	$session = icontext()->getContextDataByKey('session');
	if ($key) {
		if ($value === '__default__') {
			return $session->get($key);
		}
		if ($value === null) {
			return $session->delete($key);
		}
		$session->set($key, $value);
	} else {
		return $session;
	}
}
