<?php

irouter()->middleware(['CheckAuthMiddleware'])->group(['prefix' => '/article'], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/articleTagConfig', 'Article\ArticleTagConfigController@index');
	$route->get('/articleTagConfig/{id:\d+}', 'Article\ArticleTagConfigController@show');

	//文章专栏
	$route->get('/articleColumn/info', 'Article\ArticleColumnController@info');
	$route->post('/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/articleColumn/{id:\d+}', 'Article\ArticleColumnController@update');
});

