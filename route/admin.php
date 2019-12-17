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
	$route->post('/document/all-by-uid', 'Admin\DocumentController@getAllByUid');
	$route->middleware('DocumentPermissionMiddleware')->group(['prefix'=>'/document'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/detail', 'Admin\DocumentController@detail');
		$route->post('/operator', 'Admin\DocumentController@operator');
		$route->post('/update', 'Admin\DocumentController@update');
		$route->post('/delete', 'Admin\DocumentController@delete');
		$route->post('/create', 'Admin\DocumentController@create');
	});

	//文档内容管理
	$route->middleware('DocumentPermissionMiddleware')->group(['prefix'=>'/chapter'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/detail', 'Admin\ChapterController@detail');
		$route->post('/create', 'Admin\ChapterController@create');
		$route->post('/update', 'Admin\ChapterController@update');
		$route->post('/content', 'Admin\ChapterController@content');
		$route->post('/save', 'Admin\ChapterController@save');
		$route->post('/delete', 'Admin\ChapterController@delete');
		$route->post('/search', 'Admin\ChapterController@search');
		$route->post('/sort', 'Admin\ChapterController@sort');
	});

	//搜索用户
	$route->post('/user/search', 'Admin\UserController@search');

	$route->middleware('DocumentPermissionMiddleware')->group(['prefix'=>'/user'], function (\W7\Core\Route\Route $route){
		//文档管理设置
		$route->post('/add', 'Admin\UserController@add');
		$route->post('/detail-by-id', 'Admin\UserController@detailById');
		$route->post('/update', 'Admin\UserController@update');
		$route->post('/delete-by-ids', 'Admin\UserController@deleteByIds');
		$route->post('/batch-update-permission', 'Admin\UserController@batchUpdateDocPermissionByUid');
	});

	$route->middleware('CheckFounderMiddleware')->group([], function (\W7\Core\Route\Route $route){
		$route->post('/setting/cos', 'Admin\SettingController@cos');
		$route->post('/setting/save', 'Admin\SettingController@save');
	});

	//图片上传
	$route->middleware('DocumentPermissionMiddleware')->post('/upload/image', 'Admin\UploadController@image');
});
