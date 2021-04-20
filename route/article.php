<?php

irouter()->middleware(['CheckAuthMiddleware'])->group([], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/article/articleTagConfig', 'Article\ArticleTagConfigController@index');
	$route->get('/article/articleTagConfig/{id:\d+}', 'Article\ArticleTagConfigController@show');

	//文章专栏
	$route->get('/article/articleColumn/info', 'Article\ArticleColumnController@info');
	$route->post('/article/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/article/articleColumn/{id:\d+}', 'Article\ArticleColumnController@update');

	//文章
	$route->get('/article', 'Article\ArticleController@index');
	$route->get('/article/{id:\d+}', 'Article\ArticleController@show');
	$route->post('/article', 'Article\ArticleController@store');
	$route->put('/article/{id:\d+}', 'Article\ArticleController@update');
});

