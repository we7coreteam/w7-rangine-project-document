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
if (!function_exists('auth')) {
	function auth($documentId)
	{
		/**
		 * 需要在初始化document_user_auth后执行
		 * App::getApp()->getContext()->setRequest($request);
		 */
		$auth = App::getApp()->getContext()->getRequest()->document_user_auth;
		var_dump($auth);
		if (APP_AUTH_ALL != $auth && !in_array($documentId, $auth)) {
			throw new \Exception('无权操作');
		}
	}
}
