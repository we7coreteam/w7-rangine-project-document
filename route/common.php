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

use W7\App;
use function GuzzleHttp\Psr7\build_query;

irouter()->any('/oauth/login', function () {
	$request = App::getApp()->getContext()->getRequest();
	$query = $request->getQueryParams();

	return App::getApp()->getContext()->getResponse()->redirect(ienv('API_HOST') . 'login?' . build_query($query));
});

//获取验证码
irouter()->post('/common/verifycode/image', 'Common\VerifyCodeController@image');

//登录退出
irouter()->post('/common/auth/login', 'Common\AuthController@login');
irouter()->post('/common/auth/method', 'Common\AuthController@method');

irouter()->get('/common/auth/getlogouturl', 'Common\AuthController@getlogouturl');
irouter()->get('/common/auth/logout', 'Common\AuthController@logout');
irouter()->post('/common/auth/logout', 'Common\AuthController@logout');
irouter()->middleware(App\Middleware\CheckAuthMiddleware::class)
	->post('/common/auth/user', 'Common\AuthController@user');
irouter()->middleware(App\Middleware\CheckAuthMiddleware::class)
	->post('/common/auth/unbind', 'Common\AuthController@unbind'); //解绑

irouter()->post('/common/auth/third-party-login', 'Common\AuthController@thirdPartyLogin');
irouter()->post('/common/auth/changeThirdPartyUser', 'Common\AuthController@changeThirdPartyUser');
irouter()->post('/common/auth/bindThirdPartyUser', 'Common\AuthController@bindThirdPartyUser');
irouter()->post('/common/auth/ThirdPartyUserCacheIn', 'Common\AuthController@ThirdPartyUserCacheIn');
irouter()->post('/common/auth/third-party-login-bind', 'Common\AuthController@thirdPartyLoginBind');
irouter()->post('/common/auth/default-login-url', 'Common\AuthController@defaultLoginUrl');

irouter()->post('/menu/setting', 'Common\MenuController@setting');
//上传图片
irouter()->post('/common/upload/image', 'Admin\UploadController@image');
irouter()->middleware(App\Middleware\CheckAuthMiddleware::class)->add(['options', 'post', 'get'], '/common/uEditor', 'Admin\UploadController@uEditor');

//
irouter()->get('/user/info', 'Common\UserController@info');
irouter()->post('/user/update', 'Common\UserController@update');
irouter()->get('/user/operate', 'Common\UserController@operate');
irouter()->get('/user/followers', 'Common\UserController@followers');
irouter()->get('/user/followings', 'Common\UserController@followings');

// 用户动态
irouter()->get('/user/userStatus', 'User\UserStatusController@index');

//消息
irouter()->middleware(App\Middleware\CheckAuthMiddleware::class)->group([], function (\W7\Core\Route\Router $route) {
	$route->get('/message', 'Message\MessageController@index');
	$route->get('/message/{id:\d+}', 'Message\MessageController@show');
	$route->post('/message/read', 'Message\MessageController@read');
	$route->post('/message/readAll', 'Message\MessageController@readAll');

	//关注
	$route->post('/user/follow', 'Common\UserController@follow');
	$route->post('/user/unFollow', 'Common\UserController@unFollow');
	$route->get('/user/isFollowing', 'Common\UserController@isFollowing');
});

//install
irouter()->post('/install/systemDetection', 'Install\IndexController@systemDetection');
irouter()->post('/install/install', 'Install\IndexController@install');
irouter()->post('/install/config', 'Install\IndexController@config');
