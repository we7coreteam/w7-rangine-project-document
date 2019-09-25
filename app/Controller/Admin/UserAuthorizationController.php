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

use W7\App\Model\Entity\Document;
use W7\App\Model\Logic\UserAuthorizationLogic;
use W7\Http\Message\Server\Request;

class UserAuthorizationController extends Controller
{
	public function __construct()
	{
		$this->logic = new UserAuthorizationLogic();
	}

	public function inviteUser(Request $request)
	{
		try {
			$this->validate($request, [
				'document_id' => 'required|integer|min:1',
				'user_id' => 'required|integer|min:1',
			], [
				'document_id.required' => '文档id必填',
				'document_id.min' => '文档id非法',
				'user_id.required' => '用户id必填',
				'user_id.min' => '用户id非法',
			]);
			$user_id = $request->input('user_id');
			$document_id = $request->input('document_id');
			$result = $this->logic->inviteUser($user_id, $document_id);

			return $this->success($result, '邀请成功');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}

	public function leaveDocument(Request $request)
	{
		try {
			$this->validate($request, [
				'document_id' => 'required|integer|min:1',
				'user_id' => 'required|integer|min:1',
			], [
				'document_id.required' => '文档id必填',
				'document_id.min' => '文档id非法',
				'user_id.required' => '用户id必填',
				'user_id.min' => '用户id非法',
			]);
			$document_id = $request->input('document_id');
			$user_id = $request->input('user_id');
			$this->logic->leaveDocument($user_id, $document_id);

			return $this->success([], '从文档中删除用户成功');
		} catch (\Exception $e) {
			return $this->error($e->getMessage());
		}
	}
}
