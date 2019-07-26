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

irouter()->middleware(['AdminMiddleware','EventMiddleware'])->group(['prefix'=>'/admin'], function (\W7\Core\Route\Route $route) {
	$route->post('/login/signout', 'Admin\LoginController@signout'); // 退出登录

	$route->post('/user/getuserlist', 'Admin\UserController@getUserlist');
	$route->get('/user/getuserdoclist', 'Admin\UserController@getUserDocList');
	$route->post('/user/adduser', 'Admin\UserController@addUser');
	$route->post('/user/updateuser', 'Admin\UserController@updateUser');
	$route->post('/user/deluser', 'Admin\UserController@delUser');
	$route->post('/user/updateuserpass', 'Admin\UserController@updateUserpass');
	$route->post('/user/searchuser', 'Admin\UserController@searchUser');

	$route->get('/chapter/index', 'Admin\ChapterController@index'); //文档列表
	$route->post('/chapter/save_content', 'Admin\ChapterController@saveContent'); //文档详情
	$route->post('/chapter/publish_or_cancel', 'Admin\ChapterController@publishOrCancel'); //发布－取消文档
	$route->post('/chapter/create', 'Admin\ChapterController@create'); //新增文档
	$route->post('/chapter/update', 'Admin\ChapterController@update'); //修改文档
	$route->post('/chapter/destroy', 'Admin\ChapterController@destroy'); //删除文档

	$route->post('/upload/image', 'Admin\UploadController@image'); //图片上传

	$route->get('/category/getlist', 'Admin\CategoryController@getlist');
	$route->get('/category/getcatalogue', 'Admin\CategoryController@getCatalogue'); // 目录列表
	$route->post('/category/add', 'Admin\CategoryController@add'); // 添加类别
	$route->post('/category/getdetails', 'Admin\CategoryController@getdetails');
	$route->post('/category/update', 'Admin\CategoryController@update');
	$route->get('/category/del', 'Admin\CategoryController@del');

	$route->get('/auth/index', 'Admin\UserAuthorizationController@index');
	$route->post('/auth/update', 'Admin\UserAuthorizationController@update');

	$route->get('/document/getlist', 'Admin\DocumentController@getlist');
	$route->post('/document/create', 'Admin\DocumentController@create');
	$route->post('/document/update', 'Admin\DocumentController@update');
	$route->get('/document/getdetails', 'Admin\DocumentController@getdetails');
	$route->post('/document/del', 'Admin\DocumentController@del');
	$route->get('/document/search', 'Admin\DocumentController@search');
});
