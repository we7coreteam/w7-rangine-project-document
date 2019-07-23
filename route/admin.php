<?php

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){
    $route->post('/user/adduser', 'Admin\UserController@addUser');
    $route->post('/user/updateuser', 'Admin\UserController@updateUser');
    $route->get('/user/softdeluser', 'Admin\UserController@softdelUser');
    $route->get('/user/deluser', 'Admin\UserController@delUser');

    $route->get('/verificationcode/getcodeimg', 'Admin\VerificationcodeController@getCodeimg');
    $route->get('/verificationcode/getcode', 'Admin\VerificationcodeController@getCode');

    $route->get('/document/index', 'Admin\DocumentController@index');
    $route->post('/document/create', 'Admin\DocumentController@create');
    $route->post('/document/update', 'Admin\DocumentController@update');

    $route->get('/auth/index', 'Admin\UserAuthorizationController@index');
    $route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});


