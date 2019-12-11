<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
use W7\App\Model\Entity\User;
use W7\App\Model\Logic\DocumentPermissionLogic;
use W7\Http\Message\Server\Request;

class DocumentPermissionController extends BaseController
{
	public function getAclList(Request $request)
	{
		$list = DocumentPermissionLogic::instance()->getAclList();
		return $this->data($list);
	}

	public function add(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$params = $this->validate($request, [
			'document_id' => 'required',
			'user_id' => 'required',
			'permission' => 'required'
		]);

		try {
			DocumentPermissionLogic::instance()->add($params['document_id'], $params['user_id'], $params['permission']);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function batchAdd(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isFounder) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$params = $this->validate($request, [
			'document_permission' => 'required'
		]);

		try {
			DocumentPermissionLogic::instance()->batchAdd($params['document_permission']);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function editPermissionById(Request $request)
	{
		/**
		 * @var User $user
		 */
		$user = $request->getAttribute('user');
		if (!$user->isManager) {
			throw new ErrorHttpException('您没有权限管理该文档');
		}

		$params = $this->validate($request, [
			'id' => 'required',
			'permission' => 'required'
		]);

		try {
			DocumentPermissionLogic::instance()->updatePermissionById($params['id'], $params['permission']);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}