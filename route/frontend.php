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

irouter()->middleware(\W7\App\Middleware\FrontendDocumentPermissionMiddleware::class)->group(['prefix' => '/document'], function (\W7\Core\Route\Router $route) {
	$route->post('/all', 'Document\DocumentController@all');
	$route->post('/detail', 'Document\DocumentController@detail');
	$route->post('/chapter/list', 'Document\ChapterController@catalog');
	$route->post('/chapter/detail', 'Document\ChapterController@detail');
	$route->post('/chapter/search', 'Document\ChapterController@search');
	$route->post('/chapter/ruleDemo', 'Document\ChapterController@ruleDemo');
	$route->post('/feedback/add', 'Document\FeedbackController@add');
	$route->get('/home', 'Document\DocumentHomeController@getDocumentHome');
	$route->post('/home/search', 'Document\DocumentHomeController@search');
	$route->get('/home/check', 'Document\DocumentHomeController@checkHome');
	$route->get('/home/search-hot', 'Document\DocumentHomeController@getSearchHot');
	$route->get('/history/all', 'Document\HistoryController@all');
	$route->get('/history/detail', 'Document\HistoryController@detail');
	$route->get('/history/chapter/list', 'Document\HistoryChapterController@catalog');
	$route->get('/history/chapter/detail', 'Document\HistoryChapterController@detail');
	$route->get('/statistics', 'Document\DocumentController@statistics');
});

irouter()->middleware(\W7\App\Middleware\CheckAuthMiddleware::class)->group(['prefix' => '/article'], function (\W7\Core\Route\Router $route) {
	//系统标签配置
	$route->get('/articleTagConfig/{id:\d+}', 'Article\ArticleTagConfigController@show');

	//文章专栏
	$route->get('/articleColumn/infoMy', 'Article\ArticleColumnController@infoMy');
	$route->post('/articleColumn', 'Article\ArticleColumnController@store');
	$route->put('/articleColumn/{id:\d+}', 'Article\ArticleColumnController@update');

	//专栏关注
	$route->get('/articleColumnSub', 'Article\ArticleColumnSubController@index');
	$route->post('/articleColumnSub/info', 'Article\ArticleColumnSubController@info');
	$route->post('/articleColumnSub/sub', 'Article\ArticleColumnSubController@sub');
	$route->post('/articleColumnSub/unSub', 'Article\ArticleColumnSubController@unSub');

	//文章
	$route->get('/indexMy', 'Article\ArticleController@indexMy');
	$route->post('/article', 'Article\ArticleController@store');
	$route->put('/{id:\d+}', 'Article\ArticleController@update');
	$route->delete('/{id:\d+}', 'Article\ArticleController@destroy');

	//文章点赞
	$route->post('/articlePraise/praise', 'Article\ArticlePraiseController@praise');
	$route->post('/articlePraise/unPraise', 'Article\ArticlePraiseController@unPraise');

	//文章评论
	$route->post('/articleComment', 'Article\ArticleCommentController@store');

	//文章点赞
	$route->post('/articlePraise/info', 'Article\ArticlePraiseController@info');
	//评论点赞
	$route->post('/commentPraise/info', 'Article\CommentPraiseController@info');
	$route->post('/commentPraise/praise', 'Article\CommentPraiseController@praise');
	$route->post('/commentPraise/unPraise', 'Article\CommentPraiseController@unPraise');

	//文章收藏
	$route->get('/articleCollection/info', 'Article\ArticleCollectionController@info');
	$route->post('/articleCollection/collection', 'Article\ArticleCollectionController@collection');
	$route->post('/articleCollection/unCollection', 'Article\ArticleCollectionController@unCollection');
});

irouter()->group([], function (\W7\Core\Route\Router $route) {
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

	$route->get('/article/articleColumnSub/userSub', 'Article\ArticleColumnSubController@getUserSub');
	$route->get('/article/articleCollection/all', 'Article\ArticleCollectionController@index');
});
