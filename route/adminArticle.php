<?php

irouter()->middleware(['CheckAdminMiddleware'])->group(['prefix' => '/admin'], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/article/articleTagConfig', 'Admin\Article\ArticleTagConfigController@index');
	$route->get('/article/articleTagConfig/{id:\d+}', 'Admin\Article\ArticleTagConfigController@show');
	$route->post('/article/articleTagConfig', 'Admin\Article\ArticleTagConfigController@store');
	$route->put('/article/articleTagConfig/{id:\d+}', 'Admin\Article\ArticleTagConfigController@update');

	//文章专栏
	$route->get('/article/articleColumn', 'Admin\Article\ArticleColumnController@index');
	$route->get('/article/articleColumn/{id:\d+}', 'Admin\Article\ArticleColumnController@show');
	$route->post('/article/articleColumn', 'Admin\Article\ArticleColumnController@store');
	$route->put('/article/articleColumn/{id:\d+}', 'Admin\Article\ArticleColumnController@update');
});
