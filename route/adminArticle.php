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

irouter()->middleware(\W7\App\Middleware\CheckAdminMiddleware::class)->group(['prefix' => '/admin'], function (\W7\Core\Route\Router $route) {
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

	//文章
	$route->get('/article', 'Admin\Article\ArticleController@index');
	$route->get('/article/{id:\d+}', 'Admin\Article\ArticleController@show');
	$route->post('/article/success', 'Admin\Article\ArticleController@success');
	$route->post('/article/reject', 'Admin\Article\ArticleController@reject');
});
