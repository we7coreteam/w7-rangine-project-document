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

irouter()->middleware(['AppAuthMiddleware', 'CheckAuthMiddleware'])->group(['prefix' => '/article'], function (\W7\Core\Route\Route $route) {
	$route->get('/articleColumn/info', 'Article\ArticleColumnController@info');
	$route->post('/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/articleColumn', 'Article\ArticleColumnController@update');
});
