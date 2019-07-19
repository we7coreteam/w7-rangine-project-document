<?php

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){
    $route->post('/user/adduser', 'Admin\UserController@addUser'); // 添加用户

    $route->get('/verificationcode/getcodeimg', 'Admin\VerificationcodeController@getCodeimg'); // 获取验证码图片
    $route->get('/verificationcode/getcode', 'Admin\VerificationcodeController@getCode'); // 获取验证码

    $route->get('/document/index', 'Admin\DocumentController@index');
    $route->get('/document/create', 'Admin\DocumentController@create');

    $route->get('/auth/index', 'Admin\UserAuthorizationController@index');
    $route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});


