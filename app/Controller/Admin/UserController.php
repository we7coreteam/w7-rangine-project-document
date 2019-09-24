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

use W7\Http\Message\Server\Request;
use W7\App\Model\Logic\UserLogic;

class UserController extends Controller
{
	public function __construct()
	{
		$this->logic = new UserLogic();
	}

	public function getUserList(Request $request)
	{
		try {
			$username = trim($request->input('username'));
			$res = $this->logic->getUserList($request->input('page'), $username);
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function getUser(Request $request)
	{
		try {
			$userId = $request->session->get('user_id');
			$res = $this->logic->getUser(['id' => $userId]);
			$res = $this->logic->handleUser([$res]);
			return $this->success($res[0]);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function addUser(Request $request)
	{
		try {
			$this->logic->userAuth($request->document_user_auth);

			$this->validate($request, [
				'username' => 'required',
				'userpass' => 'required',
			], [
				'username.required' => '请输入用户姓名',
				'userpass.required' => '请输入用户密码',
			]);
			$username = trim($request->input('username'));
			$userpass = trim($request->input('userpass'));
			$data = [
				'username' => $username,
				'userpass' => $userpass,
			];
			if ($request->input('remark') !== null) {
				$data['remark'] = $request->input('remark');
			}

			$res = $this->logic->createUser($data);
			if ($res) {
				return $this->success($res);
			}
			return $this->error('用户名重复，获取数据有误');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function detailsUser(Request $request)
	{
		try {
			$this->logic->userAuth($request->document_user_auth);

			$this->validate($request, [
				'id' => 'required'
			], [
				'id.required' => '用户ID不能为空',
			]);

			$res = $this->logic->detailsUser($request->input('id'));
			if ($res) {
				return $this->success($res);
			}
			return $this->error('用户不存在');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function updateUser(Request $request)
	{
		try {
			$this->logic->userAuth($request->document_user_auth);

			$this->validate($request, [
				'id' => 'required',
				'username' => 'required',
				'userpass' => 'required',
				'confirm_userpass' => 'required',
				'has_privilege' => '',
				'remark' => '',
			], [
				'id.required' => '用户ID不能为空',
				'username.required' => '用户名不能为空',
				'userpass.required' => '密码不能为空',
				'confirm_userpass.required' => '确认密码不能为空',
			]);

			$username = trim($request->input('username'));
			$userpass = trim($request->input('userpass'));
			$confirm_userpass = trim($request->input('confirm_userpass'));

			$this->logic->checkUserpass($userpass, $confirm_userpass);

			$userinfos = $this->logic->getUser(['username'=>$username]);

			if ($userinfos) {
				$this->logic->checkUsername($userinfos['id'], intval($request->input('id')));
			}

			$data = [];
			$data['username'] = $username;
			$data['userpass'] = $userpass;

			if ($request->input('is_ban') !== null) {
				$data['is_ban'] = $request->input('is_ban');
			}
			if ($request->input('has_privilege') !== null) {
				$data['has_privilege'] = $request->input('has_privilege');
			}
			if ($request->input('remark') !== null) {
				$data['remark'] = $request->input('remark');
			}
			$res = $this->logic->updateUser(intval($request->input('id')), $data);
			if ($res) {
				return $this->success($res);
			}
			return $this->error($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function deleteUser(Request $request)
	{
		try {
			$this->logic->userAuth($request->document_user_auth);

			$this->validate($request, [
				'ids' => 'required'
			], [
				'ids.required' => 'ID不能为空',
			]);
			$ids = array_filter(explode(',', trim($request->input('ids'))));
			if ($ids) {
				$res = $this->logic->deleteUsers($ids);
				return $this->success($res);
			}
			return $this->error('参数有误');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
