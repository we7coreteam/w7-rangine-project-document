<?php

namespace W7\App\Controller\Admin;

use W7\App\Controller\BaseController;
use W7\App\Exception\ErrorHttpException;
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

	public function getListByDocIdAndUid(Request $request)
	{
		$params = $this->validate($request, [
			'document_id' => 'required'
		]);

		try {
			$list = DocumentPermissionLogic::instance()->getListByDocId($params['document_id']);
			return $this->data($list);
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}

	public function editPermissionById(Request $request)
	{
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

	public function deletePermissionById(Request $request)
	{
		$params = $this->validate($request, [
			'id' => 'required'
		]);

		try {
			DocumentPermissionLogic::instance()->deleteById($params['id']);
			return $this->data('success');
		} catch (\Throwable $e) {
			throw new ErrorHttpException($e->getMessage());
		}
	}
}