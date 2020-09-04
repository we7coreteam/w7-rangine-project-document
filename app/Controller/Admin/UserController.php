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
use W7\App\Model\Entity\DocumentPermission;
use W7\App\Model\Entity\Setting;
use W7\App\Model\Entity\User;
use W7\App\Model\Entity\UserOperateLog;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\Http\Message\Server\Request;
use W7\App\Model\Logic\UserLogic;

class UserController extends BaseController
{
	/**
	 * @api {post} /admin/user/all 所有用户
	 *
	 * @apiName all
	 * @apiGroup user
	 *
	 * @apiParam {String} username 用户名
	 *
	 */
	public function all(Request $request)
	{
		$username = trim($request->input('username'));

		$obj = User::query()->select(['id', 'username', 'group_id', 'created_at']);
		if ($username) {
			$obj->where('username', 'LIKE', "%$username%");
		}

		$user = $obj->get();
		$result = $user->toArray();

		return $this->data($result);
	}

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
					'role' => $row->isFounder ? '创始人' : '普通用户',
					'created_at' => $row->created_at->toDateTimeString(),
					'manage_doc_count' => DocumentPermission::query()->where('user_id', '=', $row->id)->where('permission', '=', DocumentPermission::MANAGER_PERMISSION)->count(),
					'operate_doc_count' => DocumentPermission::query()->where('user_id', '=', $row->id)->where('permission', '=', DocumentPermission::OPERATOR_PERMISSION)->count(),
					'read_doc_count' => DocumentPermission::query()->where('user_id', '=', $row->id)->where('permission', '=', DocumentPermission::READER_PERMISSION)->count()
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

	public function getById(Request $request)
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
			$res = UserLogic::instance()->getByUid($params['id']);
			if (!$res) {
				throw new \RuntimeException('用户不存在');
			}
			unset($res->userpass);
			return $this->data($res->toArray());
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	/**
	 * 用户编辑用户信息，如果编辑用户名，需要提供用户密码；如果编辑用户密码，需要提供原密码和新密码
	 * @param Request $request
	 * @return array
	 */
	public function updateSelf(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');

		$userName = trim($request->post('username'));
		$userPass = trim($request->post('userpass'));
		$userOldPass = trim($request->post('old_userpass'));
		if (empty($userName) && empty($userPass)) {
			throw new ErrorHttpException('参数错误');
		}
		if ($userOldPass && $user->userpass != UserLogic::instance()->userPwdEncryption($user->username, $userOldPass)) {
			throw new ErrorHttpException('旧密码错误');
		}

		$updateUser['id'] = $user->id;
		$updateUser['username'] = empty($userName) ? $user->username : $userName;
		!empty($updateUser['username']) && $updateUser['userpass'] = $userOldPass;
		$userPass && $updateUser['userpass'] = $userPass;
		try {
			$res = UserLogic::instance()->updateUser($updateUser);
			return $this->data($res);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	/**
	 * 管理员编辑用户信息
	 * @param Request $request
	 * @return array
	 */
	public function updateById(Request $request)
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
			'userpass' => 'required|confirmed',
			'remark' => '',
		], [
			'id.required' => '用户ID不能为空',
			'username.required' => '用户名不能为空',
			'userpass.required' => '密码不能为空'
		]);
		unset($user['userpass_confirmation']);
		$user['username'] = trim($user['username']);
		$user['userpass'] = trim($user['userpass']);
		if ($request->input('is_ban') !== null) {
			$user['is_ban'] = $request->input('is_ban');
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
			$delNum = UserLogic::instance()->deleteByIds($ids);
			return $this->data('成功删除' . $delNum . '用户，如果用户有文档不能直接删除');
		}
		throw new ErrorHttpException('参数有误');
	}

	public function batchUpdateDocPermissionByUid(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('您没有权限管理该文档', [], Setting::ERROR_NO_POWER);
		}

		$params = $this->validate($request, [
			'document_permission' => 'required',
			'user_id' => 'required'
		]);

		try {
			idb()->beginTransaction();
			try {
				foreach ($params['document_permission'] as $documentPermission) {
					$permission = DocumentPermissionLogic::instance()->updateByDocIdAndUid($documentPermission['document_id'], $params['user_id'], $documentPermission['permission']);
					if ($permission) {
						if (!empty($documentPermission['permission'])) {
							$remark = '设置用户' . $permission->user->username . '为' . $permission->aclName;
						} else {
							$remark = '删除用户' . $permission->user->username . '的' . $permission->aclName . '权限';
						}
						UserOperateLog::query()->create([
							'user_id' => $user->id,
							'document_id' => $documentPermission['document_id'],
							'chapter_id' => 0,
							'operate' => UserOperateLog::EDIT,
							'target_user_id' => $params['user_id'],
							'remark' => $user->username . $remark
						]);
					}
				}
				idb()->commit();
			} catch (\Throwable $e) {
				idb()->rollBack();
				throw $e;
			}

			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}
