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

irouter()->middleware(['CheckAuthMiddleware'])->group([], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/article/articleTagConfig', 'Article\ArticleTagConfigController@index');
	$route->get('/article/articleTagConfig/{id:\d+}', 'Article\ArticleTagConfigController@show');

	//文章专栏
	$route->post('/article/articleColumn/infoUser', 'Article\ArticleColumnController@infoUser');
	$route->get('/article/articleColumn/infoMy', 'Article\ArticleColumnController@infoMy');
	$route->post('/article/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/article/articleColumn/{id:\d+}', 'Article\ArticleColumnController@update');

	//文章
	$route->get('/article', 'Article\ArticleController@index');
	$route->get('/article/indexMy', 'Article\ArticleController@indexMy');
	$route->get('/article/{id:\d+}', 'Article\ArticleController@show');
	$route->post('/article', 'Article\ArticleController@store');
	$route->put('/article/{id:\d+}', 'Article\ArticleController@update');
	$route->delete('/article/{id:\d+}', 'Article\ArticleController@destroy');

});
