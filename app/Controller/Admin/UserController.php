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

		$data = $this->validate($request, [
			'username' => 'required',
			'userpass' => 'required',
		], [
			'username.required' => '请输入用户姓名',
			'userpass.required' => '请输入用户密码',
		]);
		$data = [
			'username' => trim($data['username']),
			'userpass' => trim($data['userpass']),
		];
		$data['remark'] = $request->input('remark', '');

		try {
			$res = UserLogic::instance()->createUser($data);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
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

		$params = $this->validate($request, [
			'id' => 'required'
		], [
			'id.required' => '用户ID不能为空',
		]);

		try {
			$res = UserLogic::instance()->detailById($params['id']);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
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

		$user = $this->validate($request, [
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

		$user['username'] = trim($user['username']);
		$user['userpass'] = trim($user['userpass']);
		$user['confirm_userpass'] = trim($user['confirm_userpass']);
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
			$res = UserLogic::instance()->updateUser($user);
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

		$params = $this->validate($request, [
			'ids' => 'required'
		], [
			'ids.required' => 'ID不能为空',
		]);

		$ids = array_filter(explode(',', trim($params['ids'])));
		if ($ids) {
			$delNum = UserLogic::instance()->deleteUsers($ids);
			return $this->data('成功删除' . $delNum . '用户，如果用户有文档不能直接删除');
		}
		throw new ErrorHttpException('参数有误');
	}
}
