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

irouter()->get('/js/php/controller.php', 'Admin\UploadController@index');
irouter()->post('/js/php/controller.php', 'Admin\UploadController@image');

irouter()->middleware(['CheckAuthMiddleware'])->group(['prefix'=>'/admin'], function (\W7\Core\Route\Route $route) {
	//管理文档列表
	$route->post('/document/all', 'Admin\DocumentController@all');
	$route->middleware('DocumentPermissionMiddleware')->group(['prefix'=>'/document'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/detail', 'Admin\DocumentController@detail');
		$route->post('/operator', 'Admin\DocumentController@operator');
	});

	//搜索用户
	$route->post('/user/search', 'Admin\UserController@search');
	$route->middleware('DocumentPermissionMiddleware')->group(['prefix'=>'/user'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/add', 'Admin\UserController@add');
		$route->post('/detail-by-id', 'Admin\UserController@detailById');
		$route->post('/update', 'Admin\UserController@update');
		$route->post('/delete-by-ids', 'Admin\UserController@deleteByIds');
	});
});

irouter()->middleware(['AdminMiddleware','EventMiddleware'])->group(['prefix'=>'/admin'], function (\W7\Core\Route\Route $route) {
	$route->post('/login/signout', 'Admin\LoginController@signOut'); // 退出登录

	$route->post('/user/searchuser', 'Admin\UserController@searchUser');

	$route->post('/chapter/index', 'Admin\ChapterController@index');
	$route->post('/chapter/get_content', 'Admin\ChapterController@getContent');
	$route->post('/chapter/save_content', 'Admin\ChapterController@saveContent');
	$route->post('/chapter/publish_or_cancel', 'Admin\ChapterController@publishOrCancel');
	$route->middleware(['CheckRepeatRequestMiddleware'])->post('/chapter/create', 'Admin\ChapterController@create');
	$route->post('/chapter/update', 'Admin\ChapterController@update');
	$route->post('/chapter/destroy', 'Admin\ChapterController@destroy');
	$route->post('/chapter/search', 'Admin\ChapterController@searchChapter');

	$route->post('/upload/image', 'Admin\UploadController@image'); //图片上传

	$route->post('/auth/invite_user', 'Admin\UserAuthorizationController@inviteUser');
	$route->post('/auth/leave_document', 'Admin\UserAuthorizationController@leaveDocument');

	$route->post('/document/getdocuserlist', 'Admin\DocumentController@getDocUserList');
	$route->post('/document/create', 'Admin\DocumentController@create');
	$route->post('/document/update', 'Admin\DocumentController@update');

	$route->post('/document/delete', 'Admin\DocumentController@delete');
	$route->post('/document/search', 'Admin\DocumentController@search');

	$route->post('/setting/show', 'Admin\SettingController@show');
	$route->post('/setting/save', 'Admin\SettingController@save');
});
