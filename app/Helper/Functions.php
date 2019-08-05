<?php

use W7\App;

/**
 * 公共函数，已加载
 */
function cache()
{
	$manager = new App\Model\Service\Cache\CacheManager();
	return $manager->store();
}