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

$route = irouter();
$route->middleware([\W7\App\Middleware\AppAuthMiddleware::class, \W7\App\Middleware\CheckAuthMiddleware::class])->group(['prefix' => '/admin'], function ($route) {
	//管理文档列表
	$route->post('/document/all', 'Admin\DocumentController@all');
	$route->post('/document/all-by-uid', 'Admin\DocumentController@getAllByUid');
	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/document'], function ($route) {
		//文档管理设置
		$route->post('/detail', 'Admin\DocumentController@detail');
		$route->post('/operator', 'Admin\DocumentController@operator');
		$route->post('/update', 'Admin\DocumentController@update');
		$route->post('/delete', 'Admin\DocumentController@delete');
		$route->post('/create', 'Admin\DocumentController@create');
		$route->post('/change-founder', 'Admin\DocumentController@changeDocumentFounder');
		$route->put('/change-history', 'Admin\DocumentController@changeDocumentHistory');
		//反馈建议
		$route->post('/new-feedback', 'Admin\DocumentController@checkNewFeed');
		$route->post('/feedback-list', 'Admin\FeedbackController@getList');
		$route->post('/feedback-detail', 'Admin\FeedbackController@detail');
	});

	//api文档内容管理-公共
	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/document/chapterapi'], function ($route) {
		$route->get('/getApiLabel', 'Admin\Document\ChapterApiController@getApiLabel');
		//修改接口 data 数据
		$route->post('/setApiData', 'Admin\Document\ChapterApiDataController@setData');
	});
	//文档内容管理
	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/chapter'], function ($route) {
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
		$route->post('/import', 'Admin\ChapterController@import');
	});

	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/operate-log'], function ($route) {
		$route->post('/get-user-read-log', 'Admin\UserOperateLogController@getUserReaderLog');
		$route->post('/get-by-document', 'Admin\UserOperateLogController@getByDocument');
		$route->post('/delete-by-id', 'Admin\UserOperateLogController@deleteById');
	});

	$route->group(['prefix' => '/share'], function ($route) {
		$route->post('/all', 'Admin\UserShareController@all');
		$route->post('/url', 'Admin\UserShareController@shareUrl');
		$route->get('/articleUrl', 'Admin\UserShareController@articleShareUrl');
	});

	$route->group(['prefix' => '/user'], function ($route) {
		//文档管理设置
		$route->post('/all', 'Admin\UserController@all');
		$route->post('/search', 'Admin\UserController@search');
		$route->post('/add', 'Admin\UserController@add');
		$route->post('/detail-by-id', 'Admin\UserController@detailById');
		$route->post('/update-by-id', 'Admin\UserController@updateById');
		$route->post('/update-self', 'Admin\UserController@updateSelf');
		$route->post('/delete-by-ids', 'Admin\UserController@deleteByIds');
		$route->post('/batch-update-permission', 'Admin\UserController@batchUpdateDocPermissionByUid');
	});

	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/star'], function ($route) {
		//文档管理设置
		$route->post('/all', 'Admin\StarController@all');
		$route->post('/add', 'Admin\StarController@add');
		$route->post('/delete', 'Admin\StarController@delete');
	});

	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/third-party-login'], function ($route) {
		//文档管理设置
		$route->post('/all', 'Admin\ThirdPartyLoginController@all');
		$route->post('/add', 'Admin\ThirdPartyLoginController@add');
		$route->post('/detail', 'Admin\ThirdPartyLoginController@getById');
		$route->post('/update', 'Admin\ThirdPartyLoginController@updateById');
		$route->post('/delete', 'Admin\ThirdPartyLoginController@deleteById');
		$route->post('/set-default-channel', 'Admin\ThirdPartyLoginController@setDefaultLoginChannel');
		$route->post('/get-default-channel', 'Admin\ThirdPartyLoginController@getDefaultLoginChannel');
	});

	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/menu'], function ($route) {
		//文档管理设置
		$route->post('/all', 'Admin\MenuSettingController@all');
		$route->post('/add', 'Admin\MenuSettingController@add');
		$route->post('/detail', 'Admin\MenuSettingController@getById');
		$route->post('/update', 'Admin\MenuSettingController@updateById');
		$route->post('/delete', 'Admin\MenuSettingController@deleteById');
		$route->post('/set-theme', 'Admin\MenuSettingController@setTheme');
		$route->post('/get-theme', 'Admin\MenuSettingController@getTheme');
	});

	$route->middleware(\W7\App\Middleware\BackendDocumentPermissionMiddleware::class)->group(['prefix' => '/home'], function ($route) {
		//首页管理设置
		$route->get('/list', 'Admin\DocumentHomeController@getList');
		$route->post('/add', 'Admin\DocumentHomeController@addHomeData');
		$route->all('/edit', 'Admin\DocumentHomeController@editHomeData');
		$route->post('/delete', 'Admin\DocumentHomeController@delHomeData');
		$route->get('/get-type', 'Admin\DocumentHomeController@getType');
		$route->post('/search-doc', 'Admin\DocumentHomeController@queryDocument');
		$route->get('/get-set', 'Admin\HomepageSettingController@getHomePageSet');
		$route->post('/set-open', 'Admin\HomepageSettingController@setOpenHome');
		$route->post('/set-banner', 'Admin\HomepageSettingController@setHomeBanner');
		$route->post('/set-title', 'Admin\HomepageSettingController@setHomeTtile');
	});

	$route->middleware(\W7\App\Middleware\CheckFounderMiddleware::class)->group([], function ($route) {
		$route->post('/setting/cos', 'Admin\SettingController@cos');
		$route->post('/setting/save', 'Admin\SettingController@save');
		$route->get('/setting/config', 'Admin\SettingController@config');
	});

	//图片上传
	$route->post('/upload/image', 'Admin\UploadController@image');
	$route->post('/upload/multipartUpload', 'Admin\UploadController@multipartUpload');
	$route->post('/upload/multipartUploadHandle', 'Admin\UploadController@multipartUploadHandle');
	$route->post('/upload/vodUploadSign', 'Admin\UploadController@vodUploadSign');
});

$route->middleware(\W7\App\Middleware\CheckAdminMiddleware::class)->group(['prefix' => '/admin'], function ($route) {
	//系统标签配置
	$route->get('/article/articleTagConfig', 'Admin\Article\ArticleTagConfigController@index');
	$route->get('/article/articleTagConfig/{id:\d+}', 'Admin\Article\ArticleTagConfigController@show');
	$route->post('/article/articleTagConfig', 'Admin\Article\ArticleTagConfigController@store');
	$route->put('/article/articleTagConfig/{id:\d+}', 'Admin\Article\ArticleTagConfigController@update');

	//文章专栏
	$route->get('/article/articleColumn', 'Admin\Article\ArticleColumnController@index');
	$route->get('/article/articleColumn/{id:\d+}', 'Admin\Article\ArticleColumnController@show');
	$route->post('/article/articleColumn', 'Admin\Article\ArticleColumnController@store');
	$route->put('/article/articleColumn/{id:\d+}', 'Admin\Article\ArticleColumnController@update');

	//文章
	$route->get('/article', 'Admin\Article\ArticleController@index');
	$route->get('/article/{id:\d+}', 'Admin\Article\ArticleController@show');
	$route->post('/article/success', 'Admin\Article\ArticleController@success');
	$route->post('/article/reject', 'Admin\Article\ArticleController@reject');

	//视频分类
	$route->get('/video/categoryConfig', 'Admin\Video\CategoryConfigController@index');
	$route->post('/video/categoryConfig', 'Admin\Video\CategoryConfigController@store');
	$route->put('/video/categoryConfig/{id:\d+}', 'Admin\Video\CategoryConfigController@update');

	//视频轮播
	$route->get('/video/carousel', 'Admin\Video\CarouselController@index');
	$route->get('/video/carousel/{id:\d+}', 'Admin\Video\CarouselController@show');
	$route->post('/video/carousel', 'Admin\Video\CarouselController@store');
	$route->put('/video/carousel/{id:\d+}', 'Admin\Video\CarouselController@update');
	$route->delete('/video/carousel/{id:\d+}', 'Admin\Video\CarouselController@delete');

	//视频活动
	$route->get('/video/activity', 'Admin\Video\ActivityController@index');
	$route->get('/video/activity/{id:\d+}', 'Admin\Video\ActivityController@show');
	$route->post('/video/activity', 'Admin\Video\ActivityController@store');
	$route->put('/video/activity/{id:\d+}', 'Admin\Video\ActivityController@update');
	$route->delete('/video/activity/{id:\d+}', 'Admin\Video\ActivityController@delete');

	//视频
	$route->get('/video', 'Admin\VideoController@index');
	$route->put('/video/success/{id:\d+}', 'Admin\VideoController@success');
	$route->put('/video/reject/{id:\d+}', 'Admin\VideoController@reject');
});

//获取请求数据 结构
$route->get('/admin/document/chapterapi/getData', 'Admin\Document\ChapterApiDataController@getData');
