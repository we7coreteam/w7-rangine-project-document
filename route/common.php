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
irouter()->middleware('CheckAuthMiddleware')
	->post('/common/auth/user', 'Common\AuthController@user');

irouter()->post('/common/auth/third-party-login', 'Common\AuthController@thirdPartyLogin');
irouter()->post('/common/auth/changeThirdPartyUser', 'Common\AuthController@changeThirdPartyUser');
irouter()->post('/common/auth/bindThirdPartyUser', 'Common\AuthController@bindThirdPartyUser');
irouter()->post('/common/auth/ThirdPartyUserCacheIn', 'Common\AuthController@ThirdPartyUserCacheIn');
irouter()->post('/common/auth/third-party-login-bind', 'Common\AuthController@thirdPartyLoginBind');
irouter()->post('/common/auth/default-login-url', 'Common\AuthController@defaultLoginUrl');

irouter()->post('/menu/setting', 'Common\MenuController@setting');
