<?php

use W7\App;

/**
 * 公共函数，已加载
 */
function fileName($name)
{
	$uri = App::getApp()->getContext()->getRequest()->getUri();
	return $uri->getHost().':'.$uri->getPort().'/'.$name;
}
