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

irouter()->middleware(['CheckAuthMiddleware'])->group(['prefix' => '/article'], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/articleTagConfig', 'Article\ArticleTagConfigController@index');
	$route->get('/articleTagConfig/{id:\d+}', 'Article\ArticleTagConfigController@show');

	//文章专栏
	$route->get('/articleColumn/info', 'Article\ArticleColumnController@info');
	$route->post('/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/articleColumn/{id:\d+}', 'Article\ArticleColumnController@update');
});

irouter()->middleware(['CheckAdminMiddleware'])->group(['prefix' => '/admin/article'], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/articleTagConfig', 'Admin\Article\ArticleTagConfigController@index');
	$route->get('/articleTagConfig/{id:\d+}', 'Admin\Article\ArticleTagConfigController@show');
	$route->post('/articleTagConfig', 'Admin\Article\ArticleTagConfigController@store');
	$route->put('/articleTagConfig/{id:\d+}', 'Admin\Article\ArticleTagConfigController@update');

	//文章专栏
	$route->get('/articleColumn', 'Admin\Article\ArticleColumnController@index');
	$route->get('/articleColumn/{id:\d+}', 'Admin\Article\ArticleColumnController@show');
	$route->post('/articleColumn', 'Admin\Article\ArticleColumnController@store');
	$route->put('/articleColumn/{id:\d+}', 'Admin\Article\ArticleColumnController@update');
});
