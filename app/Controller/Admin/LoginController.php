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

namespace W7\App\Controller\Admin;

use W7\App\Model\Logic\UserLogic;
use W7\App\Model\Logic\VerificationcodeLogic;
use W7\App\Model\Service\EncryptorLogic;
use W7\Http\Message\Server\Request;

class LoginController extends Controller
{
	public function check(Request $request)
	{
		try {
			$this->validate($request, [
				'username' => 'required',
				'userpass' => 'required',
				'code'=> 'required',
			], [
				'username.required' => '用户名不能为空',
				'userpass.required' => '密码不能为空',
				'code.required' => '验证码不能为空',
			]);
			$code_val = $request->session->get('img_code');

			if ($request->input(strtolower('code')) != strtolower($code_val)) {
				return $this->error('请输入正确的验证码');
			}

			$this->user_longin = new UserLogic();
			$user_val = $this->user_longin->getUser(['username'=>$request->input('username')]);

			if ($user_val) {
				$user_pwd = md5(md5($request->input('username').$request->input('userpass')));
				if (isset($user_val['userpass']) && $user_val['userpass'] == $user_pwd) {
					$request->session->set('user_id', $user_val['id']);
					return $this->success();
				} else {
					return $this->error('用户名或者密码有误');
				}
			} else {
				return $this->error('用户不存在');
			}
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function signOut(Request $request)
	{
		try {
			$res = $request->session->destroy();
			if ($res) {
				return $this->success(['msg'=>'退出成功']);
			}
			return $this->error($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
