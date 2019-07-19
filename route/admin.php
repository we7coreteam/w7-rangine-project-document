<?php

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){
    $route->post('/user/adduser', 'Admin\UserController@addUser');

    $route->get('/document/index', 'Admin\DocumentController@index');
    $route->post('/document/create', 'Admin\DocumentController@create');
    $route->post('/document/update', 'Admin\DocumentController@update');

    $route->get('/auth/index', 'Admin\UserAuthorizationController@index');
    $route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});


