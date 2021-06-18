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

irouter()->middleware('FrontendDocumentPermissionMiddleware')->group(['prefix' => '/document'], function (\W7\Core\Route\Router $route) {
	$route->get('/history/all', 'Document\HistoryController@all');
	$route->get('/history/detail', 'Document\HistoryController@detail');
	$route->get('/history/chapter/list', 'Document\HistoryChapterController@catalog');
	$route->get('/history/chapter/detail', 'Document\HistoryChapterController@detail');
	$route->get('/statistics', 'Document\DocumentController@statistics');
});

irouter()->middleware('FrontendDocumentPermissionMiddleware')->group(['prefix' => '/admin'], function (\W7\Core\Route\Router $route) {
	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix' => '/document'], function (\W7\Core\Route\Router $route) {
		$route->put('/change-history', 'Admin\DocumentController@changeDocumentHistory');
	});
});
