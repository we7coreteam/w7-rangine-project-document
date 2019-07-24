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

irouter()->post('/admin/login/check', 'Admin\LoginController@check');

irouter()->get('/admin/verificationcode/getcodeimg', 'Admin\VerificationcodeController@getCodeimg');
irouter()->get('/admin/verificationcode/getcode', 'Admin\VerificationcodeController@getCode');

irouter()->middleware('AdminMiddleware')->group(['prefix'=>'/admin'], function (\W7\Core\Route\Route $route) {
	$route->post('/user/adduser', 'Admin\UserController@addUser');
	$route->post('/user/updateuser', 'Admin\UserController@updateUser');
	$route->get('/user/softdeluser', 'Admin\UserController@softdelUser');
	$route->post('/user/deluser', 'Admin\UserController@delUser');
	$route->post('/user/updateuserpass', 'Admin\UserController@updateUserpass');
	$route->post('/user/searchuser', 'Admin\UserController@searchUser');

	$route->get('/document/index', 'Admin\DocumentController@index'); //文档列表
	$route->get('/document/show', 'Admin\DocumentController@show'); //文档详情
	$route->post('/document/publish_or_cancel', 'Admin\DocumentController@publishOrCancel'); //发布－取消文档
	$route->post('/document/create', 'Admin\DocumentController@create'); //新增文档
	$route->post('/document/update', 'Admin\DocumentController@update'); //修改文档
	$route->post('/document/destroy', 'Admin\DocumentController@destroy'); //删除文档

	$route->post('/upload/image', 'Admin\UploadController@image'); //图片上传

	$route->get('/auth/index', 'Admin\UserAuthorizationController@index');
	$route->post('/auth/update', 'Admin\UserAuthorizationController@update');
});
