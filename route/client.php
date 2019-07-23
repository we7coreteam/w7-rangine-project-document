<?php

irouter()->group(['prefix'=>'/client'],function(\W7\Core\Route\Route $route){
    $route->get('/test/index', 'Client\TestController@index');

	$route->get('/document/detail', 'Client\DocumentController@detail');
	$route->get('/document/search', 'Client\DocumentController@search');
});


