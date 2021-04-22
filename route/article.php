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
	$route->post('/article/articleColumn/tags', 'Article\ArticleColumnController@tags');

	//专栏关注
	$route->get('/article/articleColumnSub', 'Article\ArticleColumnSubController@index');
	$route->post('/article/articleColumnSub/info', 'Article\ArticleColumnSubController@info');
	$route->post('/article/articleColumnSub/sub', 'Article\ArticleColumnSubController@sub');
	$route->post('/article/articleColumnSub/unSub', 'Article\ArticleColumnSubController@unSub');

	//文章
	$route->get('/article', 'Article\ArticleController@index');
	$route->get('/article/indexMy', 'Article\ArticleController@indexMy');
	$route->get('/article/{id:\d+}', 'Article\ArticleController@show');
	$route->post('/article', 'Article\ArticleController@store');
	$route->put('/article/{id:\d+}', 'Article\ArticleController@update');
	$route->delete('/article/{id:\d+}', 'Article\ArticleController@destroy');

	//文章点赞
	$route->post('/article/articlePraise/info', 'Article\ArticlePraiseController@info');
	$route->post('/article/articlePraise/praise', 'Article\ArticlePraiseController@praise');
	$route->post('/article/articlePraise/unPraise', 'Article\ArticlePraiseController@unPraise');

});
