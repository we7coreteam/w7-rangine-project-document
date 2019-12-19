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

irouter()->middleware('FrontendDocumentPermissionMiddleware')->group(['prefix' => '/document'], function (\W7\Core\Route\Route $route) {
	$route->post('/detail', 'Document\DocumentController@detail');
	$route->post('/chapter/list', 'Document\ChapterController@catalog');
	$route->post('/chapter/detail', 'Document\ChapterController@detail');
	$route->post('/chapter/search', 'Document\ChapterController@search');
});
