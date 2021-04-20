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
	//文章专栏
	$route->get('/articleColumn/info', 'Article\ArticleColumnController@info');
	$route->post('/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/articleColumn', 'Article\ArticleColumnController@update');

	//系统标签配置
	$route->get('/articleTagConfig', 'Article\ArticleColumnController@index');
	$route->get('/articleTagConfig/{id:\d+}', 'Article\ArticleColumnController@show');
	$route->post('/articleTagConfig', 'Article\ArticleColumnController@store');
	$route->put('/articleTagConfig', 'Article\ArticleColumnController@update');
});
