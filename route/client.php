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

irouter()->group(['prefix'=>'/client'], function (\W7\Core\Route\Route $route) {
	$route->get('/test/index', 'Client\TestController@index');

	$route->get('/chapters', 'Client\ChapterController@chapters');
	$route->get('/detail', 'Client\ChapterController@detail');
	$route->get('/search', 'Client\ChapterController@search');

	$route->post('/document/getshowlist', 'Client\DocumentController@getShowList');
});

