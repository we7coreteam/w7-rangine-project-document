<?php

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
