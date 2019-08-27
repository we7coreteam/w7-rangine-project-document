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
irouter()->post('/admin/verificationcode/getcodeimg', 'Admin\VerificationcodeController@getCodeimg');
irouter()->get('/js/php/controller.php', 'Admin\UploadController@index');
irouter()->post('/js/php/controller.php', 'Admin\UploadController@image');

irouter()->middleware(['AdminMiddleware','EventMiddleware'])->group(['prefix'=>'/admin'], function (\W7\Core\Route\Route $route) {
	$route->post('/login/signout', 'Admin\LoginController@signout'); // 退出登录

	$route->post('/user/getuserlist', 'Admin\UserController@getUserlist');
	$route->post('/user/getuser', 'Admin\UserController@getUser');
	$route->post('/user/adduser', 'Admin\UserController@addUser');
	$route->post('/user/updateuser', 'Admin\UserController@updateUser');
	$route->post('/user/deluser', 'Admin\UserController@delUser');
	$route->post('/user/searchuser', 'Admin\UserController@searchUser');
	$route->post('/user/detailsuser', 'Admin\UserController@detailsUser');

	$route->post('/chapter/index', 'Admin\ChapterController@index');
	$route->post('/chapter/get_content', 'Admin\ChapterController@getContent');
	$route->post('/chapter/save_content', 'Admin\ChapterController@saveContent');
	$route->post('/chapter/publish_or_cancel', 'Admin\ChapterController@publishOrCancel');
	$route->post('/chapter/create', 'Admin\ChapterController@create');
	$route->post('/chapter/update', 'Admin\ChapterController@update');
	$route->post('/chapter/destroy', 'Admin\ChapterController@destroy');
	$route->post('/chapter/search', 'Admin\ChapterController@searchChapter');

	$route->post('/upload/image', 'Admin\UploadController@image'); //图片上传

	$route->post('/auth/invite_user', 'Admin\UserAuthorizationController@inviteUser');
	$route->post('/auth/leave_document', 'Admin\UserAuthorizationController@leaveDocument');

	$route->post('/document/getlist', 'Admin\DocumentController@getlist');
	$route->post('/document/getdocuserlist', 'Admin\DocumentController@getDocUserList');
	$route->post('/document/create', 'Admin\DocumentController@create');
	$route->post('/document/update', 'Admin\DocumentController@update');
	$route->post('/document/getdetails', 'Admin\DocumentController@getdetails');
	$route->post('/document/del', 'Admin\DocumentController@del');
	$route->post('/document/search', 'Admin\DocumentController@search');
});
