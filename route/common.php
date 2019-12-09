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

//获取验证码
irouter()->post('/common/verifycode/image', 'Common\VerifyCodeController@image');

//登录退出
irouter()->post('/common/auth/login', 'Common\AuthController@login');
irouter()->middleware('CheckAuthMiddleware')->post('/common/auth/logout', 'Common\AuthController@logout');