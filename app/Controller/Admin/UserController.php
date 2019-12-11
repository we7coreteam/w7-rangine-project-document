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

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\User;
use W7\Http\Message\Server\Request;
use W7\App\Model\Logic\UserLogic;

class UserController extends BaseController
{
	public function search(Request $request)
	{
		$username = trim($request->post('username'));
		$page = intval($request->post('page'));

		$user = User::query()->where('username', 'LIKE', "%$username%")->paginate(null, '*', 'page', $page);

		$result = [];
		$list = $user->items();
		if (!empty($list)) {
			foreach ($list as $i => $row) {
				$result['data'][] = [
					'id' => $row->id,
					'username' => $row->username,
					'created_at' => $row->created_at->toDateTimeString(),
				];
			}
		}

		$result['page_count'] = $user->lastPage();
		$result['total'] = $user->total();
		$result['page_current'] = $user->currentPage();

		return $this->data($result);
	}

	public function add(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('没有操作用户的权限');
		}

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
		$data['remark'] = $request->input('remark', '');

		$res = UserLogic::instance()->createUser($data);
		if ($res) {
			return $this->data($res);
		}

		throw new ErrorHttpException('用户名重复，获取数据有误');
	}

	public function detailById(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('没有操作用户的权限');
		}

		$this->validate($request, [
			'id' => 'required'
		], [
			'id.required' => '用户ID不能为空',
		]);

		$res = UserLogic::instance()->detailById($request->input('id'));
		if ($res) {
			return $this->data($res);
		}
		throw new ErrorHttpException('用户不存在');
	}

	public function update(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('没有操作用户的权限');
		}

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

		$user['username'] = trim($request->input('username'));
		$user['userpass'] = trim($request->input('userpass'));
		$user['confirm_userpass'] = trim($request->input('confirm_userpass'));
		if ($request->input('is_ban') !== null) {
			$user['is_ban'] = $request->input('is_ban');
		}
		if ($request->input('has_privilege') !== null) {
			$user['has_privilege'] = $request->input('has_privilege');
		}
		if ($request->input('remark') !== null) {
			$user['remark'] = $request->input('remark');
		}

		try {
			$res = UserLogic::instance()->updateUser(intval($request->input('id')), $data);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function deleteByIds(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('没有操作用户的权限');
		}

		$this->validate($request, [
			'ids' => 'required'
		], [
			'ids.required' => 'ID不能为空',
		]);

		$ids = array_filter(explode(',', trim($request->input('ids'))));
		if ($ids) {
			$delNum = UserLogic::instance()->deleteUsers($ids);
			return $this->data('成功删除' . $delNum . '用户，如果用户有文档不能直接删除');
		}
		throw new ErrorHttpException('参数有误');
	}
}
