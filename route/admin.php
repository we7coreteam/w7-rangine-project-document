<?php

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'],function(\W7\Core\Route\Route $route){
    $route->post('/user/adduser', 'Admin\UserController@addUser'); // 添加用户

    $route->get('/verificationcode/getcodeimg', 'Admin\VerificationcodeController@getCodeimg'); // 获取验证码图片
    $route->get('/verificationcode/getcode', 'Admin\VerificationcodeController@getCode'); // 获取验证码

    $route->get('/document/index', 'Admin\DocumentController@index'); //文档列表
    $route->get('/document/show', 'Admin\DocumentController@show'); //文档详情
    $route->post('/document/create', 'Admin\DocumentController@create'); //新增文档
    $route->post('/document/update', 'Admin\DocumentController@update'); //修改文档
	$route->post('/document/destroy', 'Admin\DocumentController@destroy'); //删除文档

	$route->post('/upload/image', 'Admin\UploadController@image'); //删除文档

    $route->get('/auth/index', 'Admin\UserAuthorizationController@index');
    $route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});


