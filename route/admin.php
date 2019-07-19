<?php

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){
    $route->post('/user/adduser', 'Admin\UserController@addUser');

    $route->get('/document/index', 'Admin\DocumentController@index');
    $route->get('/document/create', 'Admin\DocumentController@create');

    $route->get('/auth/index', 'Admin\UserAuthorizationController@index');
    $route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});


