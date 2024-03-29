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

$route = irouter();

$route->middleware(\W7\App\Middleware\FrontendDocumentPermissionMiddleware::class)->group(['prefix' => '/document'], function ($route) {
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
});
$route->middleware(\W7\App\Middleware\CorsApiMiddleware::class)->group(['prefix' => '/document'], function ($route) {
	$route->post('/chapter/record', 'Document\ChapterController@record');
	$route->all('/mockApiReponse/{id}/{router:[\w/]+}', 'Document\MockApiReponseController@index');
});

