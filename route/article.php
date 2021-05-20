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
	$route->get('/article/articleTagConfig/{id:\d+}', 'Article\ArticleTagConfigController@show');

	//文章专栏
	$route->get('/article/articleColumn/infoMy', 'Article\ArticleColumnController@infoMy');
	$route->post('/article/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/article/articleColumn/{id:\d+}', 'Article\ArticleColumnController@update');


	//专栏关注
	$route->get('/article/articleColumnSub', 'Article\ArticleColumnSubController@index');
	$route->post('/article/articleColumnSub/info', 'Article\ArticleColumnSubController@info');
	$route->post('/article/articleColumnSub/sub', 'Article\ArticleColumnSubController@sub');
	$route->post('/article/articleColumnSub/unSub', 'Article\ArticleColumnSubController@unSub');

	//文章
	$route->get('/article/indexMy', 'Article\ArticleController@indexMy');
	$route->post('/article', 'Article\ArticleController@store');
	$route->put('/article/{id:\d+}', 'Article\ArticleController@update');
	$route->delete('/article/{id:\d+}', 'Article\ArticleController@destroy');

	//文章点赞
	$route->post('/article/articlePraise/praise', 'Article\ArticlePraiseController@praise');
	$route->post('/article/articlePraise/unPraise', 'Article\ArticlePraiseController@unPraise');

	//文章评论
	$route->post('/article/articleComment', 'Article\ArticleCommentController@store');

	//文章点赞
	$route->post('/article/articlePraise/info', 'Article\ArticlePraiseController@info');
	//评论点赞
	$route->post('/article/commentPraise/info', 'Article\CommentPraiseController@info');
	$route->post('/article/commentPraise/praise', 'Article\CommentPraiseController@praise');
	$route->post('/article/commentPraise/unPraise', 'Article\CommentPraiseController@unPraise');

	//文章收藏
	$route->get('/article/articleCollection/info', 'Article\ArticleCollectionController@info');
	$route->post('/article/articleCollection/collection', 'Article\ArticleCollectionController@collection');
	$route->post('/article/articleCollection/unCollection', 'Article\ArticleCollectionController@unCollection');
});

irouter()->get('/article/articleCollection/all', 'Article\ArticleCollectionController@index');

irouter()->group([], function (\W7\Core\Route\Route $route) {
	//系统标签配置
	$route->get('/article/articleTagConfig', 'Article\ArticleTagConfigController@index');
	//文章专栏
	$route->post('/article/articleColumn/infoUser', 'Article\ArticleColumnController@infoUser');
	$route->post('/article/articleColumn/tags', 'Article\ArticleColumnController@tags');

	//文章
	$route->get('/article', 'Article\ArticleController@index');
	$route->get('/article/{id:\d+}', 'Article\ArticleController@show');

	//文章评论
	$route->get('/article/articleComment', 'Article\ArticleCommentController@index');


});
