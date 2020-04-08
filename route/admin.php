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
irouter()->middleware(['AppAuthMiddleware', 'CheckAuthMiddleware'])->group(['prefix'=>'/admin'], function (\W7\Core\Route\Route $route) {
	//管理文档列表
	$route->post('/document/all', 'Admin\DocumentController@all');
	$route->post('/document/all-by-uid', 'Admin\DocumentController@getAllByUid');
	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/document'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/detail', 'Admin\DocumentController@detail');
		$route->post('/operator', 'Admin\DocumentController@operator');
		$route->post('/update', 'Admin\DocumentController@update');
		$route->post('/delete', 'Admin\DocumentController@delete');
		$route->post('/create', 'Admin\DocumentController@create');
		$route->post('/change-founder', 'Admin\DocumentController@changeDocumentFounder');
	});

	//api文档内容管理-公共
	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/chapterapi'], function (\W7\Core\Route\Route $route){
		$route->get('/getStatusCode', 'Admin\ChapterApiController@getStatusCode');
		$route->get('/getMethodLabel', 'Admin\ChapterApiController@getMethodLabel');
		$route->get('/getEnabledLabel', 'Admin\ChapterApiController@getEnabledLabel');
	});
	//文档内容管理
	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/chapter'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/detail', 'Admin\ChapterController@detail');
		$route->post('/create', 'Admin\ChapterController@create');
		$route->post('/update', 'Admin\ChapterController@update');
		$route->post('/content', 'Admin\ChapterController@content');
		$route->post('/save', 'Admin\ChapterController@save');
		$route->post('/delete', 'Admin\ChapterController@delete');
		$route->post('/search', 'Admin\ChapterController@search');
		$route->post('/sort', 'Admin\ChapterController@sort');
		$route->post('/default-show', 'Admin\ChapterController@defaultShow');
		$route->post('/copy', 'Admin\ChapterController@copy');
	});

	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/operate-log'], function (\W7\Core\Route\Route $route){
		$route->post('/get-user-read-log', 'Admin\UserOperateLogController@getUserReaderLog');
		$route->post('/get-by-document', 'Admin\UserOperateLogController@getByDocument');
		$route->post('/delete-by-id', 'Admin\UserOperateLogController@deleteById');
	});

	$route->group(['prefix'=>'/share'], function (\W7\Core\Route\Route $route){
		$route->post('/all', 'Admin\UserShareController@all');
		$route->post('/url', 'Admin\UserShareController@shareUrl');
	});

	$route->group(['prefix'=>'/user'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/search', 'Admin\UserController@search');
		$route->post('/add', 'Admin\UserController@add');
		$route->post('/detail-by-id', 'Admin\UserController@detailById');
		$route->post('/update-by-id', 'Admin\UserController@updateById');
		$route->post('/update-self', 'Admin\UserController@updateSelf');
		$route->post('/delete-by-ids', 'Admin\UserController@deleteByIds');
		$route->post('/batch-update-permission', 'Admin\UserController@batchUpdateDocPermissionByUid');
	});

	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/star'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/all', 'Admin\StarController@all');
		$route->post('/add', 'Admin\StarController@add');
		$route->post('/delete', 'Admin\StarController@delete');
	});

	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/third-party-login'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/all', 'Admin\ThirdPartyLoginController@all');
		$route->post('/add', 'Admin\ThirdPartyLoginController@add');
		$route->post('/detail', 'Admin\ThirdPartyLoginController@getById');
		$route->post('/update', 'Admin\ThirdPartyLoginController@updateById');
		$route->post('/delete', 'Admin\ThirdPartyLoginController@deleteById');
		$route->post('/set-default-channel', 'Admin\ThirdPartyLoginController@setDefaultLoginChannel');
		$route->post('/get-default-channel', 'Admin\ThirdPartyLoginController@getDefaultLoginChannel');
	});

	$route->middleware('BackendDocumentPermissionMiddleware')->group(['prefix'=>'/menu'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/all', 'Admin\MenuSettingController@all');
		$route->post('/add', 'Admin\MenuSettingController@add');
		$route->post('/detail', 'Admin\MenuSettingController@getById');
		$route->post('/update', 'Admin\MenuSettingController@updateById');
		$route->post('/delete', 'Admin\MenuSettingController@deleteById');
		$route->post('/set-theme', 'Admin\MenuSettingController@setTheme');
		$route->post('/get-theme', 'Admin\MenuSettingController@getTheme');
	});

	$route->middleware('CheckFounderMiddleware')->group([], function (\W7\Core\Route\Route $route){
		$route->post('/setting/cos', 'Admin\SettingController@cos');
		$route->post('/setting/save', 'Admin\SettingController@save');
	});

	//图片上传
	$route->post('/upload/image', 'Admin\UploadController@image');
});
