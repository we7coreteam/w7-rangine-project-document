<?php

irouter()->post('/admin/login/check', 'Admin\LoginController@check');

irouter()->get('/admin/verificationcode/getcodeimg', 'Admin\VerificationcodeController@getCodeimg');
irouter()->get('/admin/verificationcode/getcode', 'Admin\VerificationcodeController@getCode');

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){

    $route->post('/user/adduser', 'Admin\UserController@addUser');
    $route->post('/user/updateuser', 'Admin\UserController@updateUser');
    $route->get('/user/softdeluser', 'Admin\UserController@softdelUser');
    $route->post('/user/deluser', 'Admin\UserController@delUser');
    $route->post('/user/updateuserpass', 'Admin\UserController@updateUserpass');
    $route->post('/user/searchuser', 'Admin\UserController@searchUser');

    $route->get('/document/index', 'Admin\DocumentController@index');
    $route->post('/document/create', 'Admin\DocumentController@create');
    $route->post('/document/update', 'Admin\DocumentController@update');

    $route->get('/auth/index', 'Admin\UserAuthorizationController@index');
    $route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});


