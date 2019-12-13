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

namespace W7\App\Controller\Common;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Logic\UserLogic;
use W7\Http\Message\Server\Request;

class AuthController extends BaseController
{
	public function login(Request $request)
	{
		$data = $this->validate($request, [
			'username' => 'required',
			'userpass' => 'required',
			'code' => 'required',
		], [
			'username.required' => '用户名不能为空',
			'userpass.required' => '密码不能为空',
			'code.required' => '验证码不能为空',
		]);
		$code = $request->session->get('img_code');
		if (strtolower($data['code']) != strtolower($code)) {
			throw new ErrorHttpException('请输入正确的验证码');
		}

		$user = UserLogic::instance()->getByUserName($data['username']);
		if (empty($user)) {
			throw new ErrorHttpException('用户名或密码错误，请检查');
		}

		if ($user->userpass != UserLogic::instance()->userPwdEncryption($user->username, $data['userpass'])) {
			throw new ErrorHttpException('用户名或密码错误，请检查');
		}

		if (!empty($user->is_ban)) {
			throw new ErrorHttpException('您使用的用户已经被禁用，请联系管理员');
		}

		$request->session->destroy();

		$request->session->set('user', [
			'uid' => $user->id,
			'username' => $user->username,
		]);

		return $this->data('success');
	}

	public function logout(Request $request)
	{
		$request->session->destroy();
		return $this->data('success');
	}

	public function user(Request $request)
	{
		$userSession = $request->session->get('user');
		$user = UserLogic::instance()->getByUid($userSession['uid']);

		$result = [
			'id' => $user->id,
			'username' => $user->username,
			'created_at' => $user->created_at->toDateTimeString(),
			'updated_at' => $user->updated_at->toDateTimeString(),
		];

		return $this->data($result);
	}
}
