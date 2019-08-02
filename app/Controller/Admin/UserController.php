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
	//        C9F8QdEBAUMBFJXB24D

	public function __construct()
	{
		$this->logic = new UserLogic();
	}

	public function getUserlist()
	{
		try {
			$res = $this->logic->getUserlist();
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function addUser(Request $request)
	{
		try {
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
				'userpass' => md5(md5($username.$userpass)),
			];

			$res = $this->logic->createUser($data);
			if ($res) {
				return $this->success($res);
			}
			return $this->error($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function updateUser(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required'
			], [
				'id.required' => '用户ID不能为空',
			]);

			$data = [];
			if ($request->input('username')) {
				$data['username'] = $request->input('username');
			}
			if ($request->input('is_ban')) {
				$data['is_ban'] = $request->input('is_ban');
			}
			if ($request->input('has_privilege')) {
				$data['has_privilege'] = $request->input('has_privilege');
			}
			if ($request->input('remark')) {
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
	
	public function delUser(Request $request)
	{
		try {
			$this->validate($request, [
				'ids' => 'required'
			], [
				'ids.required' => 'ID不能为空',
			]);
			$ids = array_filter(explode(',', trim($request->input('ids'))));

			if ($ids) {
				$hasDocuments = $this->logic->hasDocuments($ids);
				return $this->success($hasDocuments);
			}
			return $this->error('参数有误');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function updateUserpass(Request $request)
	{
		try {
			$this->validate($request, [
				'id' => 'required',
				'userpass' => 'required',
			], [
				'id.required' => '用户ID不能为空',
				'userpass.required' => '密码不能为空',
			]);
			$user_val = $this->logic->getUser(['id'=>$request->input('id')]);
			if ($user_val) {
				$data = [
					'userpass' => md5(md5($user_val['username'].trim($request->input('userpass')))),
				];
				$res = $this->logic->updateUser(intval($request->input('id')), $data);
				if ($res) {
					return $this->success($res);
				}
				return $this->error($res);
			}
			return $this->error($user_val);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function searchUser(Request $request)
	{
		try {
			$this->validate($request, [
				'keyword' => 'required',
			], [
				'keyword.required' => '关键字不能为空',
			]);
			$res = $this->logic->searchUser(['username'=>trim($request->input('keyword'))]);
			return $this->success($res);
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
