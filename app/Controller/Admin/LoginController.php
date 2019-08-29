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

use W7\App\Model\Logic\LoginLongic;
use W7\Http\Message\Server\Request;

class LoginController extends Controller
{
	public $login;

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

			if (strtolower($request->input('code')) != strtolower($code_val)) {
				return $this->error('请输入正确的验证码');
			}

			$this->login = new LoginLongic();
			$data = [
				'username' => trim($request->input('username')),
				'userpass' => trim($request->input('userpass')),
			];

			$res = $this->login->check($data);
			if ($res && $res['code'] == 1) {
				$request->session->set('user_id', $res['id']);
				return $this->success();
			} else {
				return $this->error($res['msg']);
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
