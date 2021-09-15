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

$route = irouter();

$route->any('/oauth/login', function () {
	$request = \W7\Facade\Context::getRequest();
	$query = $request->getQueryParams();

	return \W7\Facade\Context::getResponse()->redirect(ienv('API_HOST') . 'login?' . build_query($query));
});

//获取验证码
$route->post('/common/verifycode/image', 'Common\VerifyCodeController@image');

//登录退出
$route->post('/common/auth/login', 'Common\AuthController@login');
$route->post('/common/auth/method', 'Common\AuthController@method');

$route->get('/common/auth/getlogouturl', 'Common\AuthController@getlogouturl');
$route->get('/common/auth/logout', 'Common\AuthController@logout');
$route->post('/common/auth/logout', 'Common\AuthController@logout');
$route->middleware(\W7\App\Middleware\CheckAuthMiddleware::class)
	->post('/common/auth/user', 'Common\AuthController@user');
$route->middleware(\W7\App\Middleware\CheckAuthMiddleware::class)
	->post('/common/auth/unbind', 'Common\AuthController@unbind'); //解绑

$route->post('/common/auth/third-party-login', 'Common\AuthController@thirdPartyLogin');
$route->post('/common/auth/changeThirdPartyUser', 'Common\AuthController@changeThirdPartyUser');
$route->post('/common/auth/bindThirdPartyUser', 'Common\AuthController@bindThirdPartyUser');
$route->post('/common/auth/ThirdPartyUserCacheIn', 'Common\AuthController@ThirdPartyUserCacheIn');
$route->post('/common/auth/third-party-login-bind', 'Common\AuthController@thirdPartyLoginBind');
$route->post('/common/auth/default-login-url', 'Common\AuthController@defaultLoginUrl');

$route->post('/menu/setting', 'Common\MenuController@setting');
//上传图片
$route->post('/common/upload/image', 'Admin\UploadController@image');

//
$route->get('/user/info', 'Common\UserController@info');
$route->post('/user/update', 'Common\UserController@update');
$route->get('/user/operate', 'Common\UserController@operate');
