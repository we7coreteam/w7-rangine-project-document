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
	return App\Model\Service\Cache\CacheManager::getStore();
}
